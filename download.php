<?php
require_once 'config.php';

/**
 * Gestionnaire principal de téléchargement
 * Gère à la fois les visiteurs et les utilisateurs avec clé API
 */

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Variables principales
$url = $_GET['url'] ?? '';
$format = $_GET['format'] ?? 'video';
$quality = $_GET['quality'] ?? 'best';
$apiKey = $_GET['api_key'] ?? '';
$csrfToken = $_GET['csrf_token'] ?? '';

$isApiRequest = !empty($apiKey);
$isVisitor = !$isApiRequest && !isset($_SESSION['user_id']);

// Validation CSRF pour les requêtes non-API
if (!$isApiRequest && !verifyCSRFToken($csrfToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Token de sécurité invalide']));
}

// Validation de l'URL
if (empty($url)) {
    http_response_code(400);
    die(json_encode(['error' => 'URL YouTube manquante']));
}

if (!isValidYouTubeURL($url)) {
    http_response_code(400);
    die(json_encode(['error' => 'URL YouTube invalide']));
}

// Validation du format
if (!in_array($format, ['audio', 'video'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Format invalide. Utilisez "audio" ou "video"']));
}

// Validation de la qualité
if (!in_array($quality, VIDEO_QUALITIES)) {
    http_response_code(400);
    die(json_encode(['error' => 'Qualité invalide. Utilisez: ' . implode(', ', VIDEO_QUALITIES)]));
}

$db = Database::getInstance()->getConnection();
$userManager = new UserManager();

$userId = null;
$userType = 'visitor';
$allowedFormats = FORMATS_VISITOR;

// Gestion des utilisateurs avec clé API
if ($isApiRequest) {
    $apiData = $userManager->validateAPIKey($apiKey);
    
    if (!$apiData) {
        http_response_code(401);
        die(json_encode(['error' => 'Clé API invalide ou inactive']));
    }
    
    $userId = $apiData['user_id'];
    $userType = $apiData['type_compte'];
    
    // Définir les formats autorisés selon le type de compte
    $allowedFormats = match($userType) {
        'gratuit' => FORMATS_GRATUIT,
        'premium' => FORMATS_PREMIUM,
        'unlimited' => FORMATS_UNLIMITED,
        default => FORMATS_GRATUIT
    };
    
    // Vérifier si le format est autorisé
    if (!in_array($format, $allowedFormats)) {
        http_response_code(403);
        die(json_encode([
            'error' => 'Format non autorisé pour votre type de compte',
            'allowed_formats' => $allowedFormats,
            'account_type' => $userType
        ]));
    }
    
    // Vérifier et mettre à jour le quota
    if (!$userManager->checkAndUpdateQuota($userId, $userType)) {
        $quotaLimit = match($userType) {
            'gratuit' => QUOTA_GRATUIT,
            'premium' => QUOTA_PREMIUM,
            'unlimited' => QUOTA_UNLIMITED,
            default => QUOTA_GRATUIT
        };
        
        http_response_code(429);
        die(json_encode([
            'error' => 'Quota quotidien dépassé',
            'quota_limit' => $quotaLimit,
            'reset_time' => 'Minuit (00:00)'
        ]));
    }
    
    // Mettre à jour l'utilisation de la clé API
    $userManager->updateAPIKeyUsage($apiKey);
    
} elseif (isset($_SESSION['user_id'])) {
    // Utilisateur connecté mais sans clé API (utilisation interface web)
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'] ?? 'gratuit';
    
    $allowedFormats = match($userType) {
        'gratuit' => FORMATS_GRATUIT,
        'premium' => FORMATS_PREMIUM,
        'unlimited' => FORMATS_UNLIMITED,
        default => FORMATS_GRATUIT
    };
    
    if (!in_array($format, $allowedFormats)) {
        http_response_code(403);
        die(json_encode([
            'error' => 'Format non autorisé pour votre type de compte',
            'allowed_formats' => $allowedFormats
        ]));
    }
    
    // Vérifier le quota pour les utilisateurs connectés
    if (!$userManager->checkAndUpdateQuota($userId, $userType)) {
        http_response_code(429);
        die(json_encode(['error' => 'Quota quotidien dépassé']));
    }
}

// Les visiteurs ont accès à tous les formats sans restriction de quota
// $allowedFormats est déjà défini à FORMATS_VISITOR pour les visiteurs

// Extraire l'ID de la vidéo
$videoId = extractYouTubeID($url);
if (!$videoId) {
    http_response_code(400);
    die(json_encode(['error' => 'Impossible d\'extraire l\'ID de la vidéo']));
}

try {
    // Construire l'URL pour l'API Node.js
    $nodeApiUrl = NODE_API_URL . '/download/' . urlencode($videoId);
    $nodeParams = http_build_query([
        'format' => $format,
        'quality' => $quality
    ]);
    
    $fullNodeUrl = $nodeApiUrl . '?' . $nodeParams;
    
    // Initialiser cURL pour la requête vers l'API Node.js
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $fullNodeUrl,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 900, // 15 minutes
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'YTDownloader-SaaS/1.0',
        CURLOPT_WRITEFUNCTION => function($ch, $data) {
            echo $data;
            return strlen($data);
        },
        CURLOPT_HEADERFUNCTION => function($ch, $header) {
            // Transférer les headers importants
            $headerLower = strtolower($header);
            if (strpos($headerLower, 'content-disposition:') === 0 ||
                strpos($headerLower, 'content-type:') === 0 ||
                strpos($headerLower, 'content-length:') === 0) {
                header(trim($header));
            }
            return strlen($header);
        }
    ]);
    
    // Obtenir les informations de la vidéo pour les logs
    $videoTitle = 'Titre indisponible';
    $fileSize = null;
    
    try {
        $infoUrl = NODE_API_URL . '/info/' . urlencode($videoId);
        $infoResponse = @file_get_contents($infoUrl, false, stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'YTDownloader-SaaS/1.0'
            ]
        ]));
        
        if ($infoResponse) {
            $videoInfo = json_decode($infoResponse, true);
            if ($videoInfo && isset($videoInfo['title'])) {
                $videoTitle = $videoInfo['title'];
            }
        }
    } catch (Exception $e) {
        // Ignorer les erreurs d'info, continuer le téléchargement
    }
    
    // Enregistrer le téléchargement en base de données
    $stmt = $db->prepare("
        INSERT INTO downloads (user_id, api_key, video_id, video_title, format, quality, file_size, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $userId,
        $isApiRequest ? $apiKey : null,
        $videoId,
        $videoTitle,
        $format,
        $quality === 'best' ? null : $quality,
        $fileSize,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
    
    // Mettre à jour les statistiques globales
    $db->exec("UPDATE site_stats SET total_downloads = total_downloads + 1");
    
    // Log de l'activité
    error_log(sprintf(
        "[YTDownloader] Download: %s | User: %s | Type: %s | Format: %s | Quality: %s | Video: %s",
        date('Y-m-d H:i:s'),
        $userId ? "ID:$userId" : ($isApiRequest ? "API:$apiKey" : 'Visitor'),
        $userType,
        $format,
        $quality,
        $videoId
    ));
    
    // Exécuter la requête vers l'API Node.js
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($result === false || !empty($error)) {
        throw new Exception("Erreur cURL: $error");
    }
    
    if ($httpCode >= 400) {
        http_response_code($httpCode);
        if ($httpCode === 404) {
            die(json_encode(['error' => 'Vidéo non trouvée ou indisponible']));
        } elseif ($httpCode === 403) {
            die(json_encode(['error' => 'Vidéo privée ou restreinte']));
        } else {
            die(json_encode(['error' => 'Erreur du serveur de téléchargement']));
        }
    }
    
} catch (Exception $e) {
    error_log("[YTDownloader] Error: " . $e->getMessage());
    http_response_code(500);
    die(json_encode(['error' => 'Erreur interne du serveur: ' . $e->getMessage()]));
}

// Le téléchargement a été traité avec succès
// Les données ont été transmises directement au navigateur via cURL
?>