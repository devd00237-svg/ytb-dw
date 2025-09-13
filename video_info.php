<?php
require_once 'config.php';

/**
 * Endpoint pour récupérer les informations d'une vidéo YouTube
 * Utilisé par l'interface web pour la prévisualisation
 */

// Headers JSON
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$url = $_GET['url'] ?? '';

// Validation de l'URL
if (empty($url)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'URL YouTube manquante'
    ]);
    exit;
}

if (!isValidYouTubeURL($url)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'URL YouTube invalide'
    ]);
    exit;
}

// Extraire l'ID de la vidéo
$videoId = extractYouTubeID($url);
if (!$videoId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Impossible d\'extraire l\'ID de la vidéo'
    ]);
    exit;
}

try {
    // Construire l'URL pour l'API Node.js
    $infoUrl = NODE_API_URL . '/info/' . urlencode($videoId);
    
    // Configuration du contexte HTTP
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 60,
            'user_agent' => 'YTDownloader-SaaS/1.0',
            'header' => 'Accept: application/json'
        ]
    ]);
    
    // Faire la requête vers l'API Node.js
    $response = @file_get_contents($infoUrl, false, $context);
    
    if ($response === false) {
        throw new Exception('Impossible de contacter le service de téléchargement');
    }
    
    // Parser la réponse JSON
    $videoInfo = json_decode($response, true);
    
    if (!$videoInfo) {
        throw new Exception('Réponse invalide du service');
    }
    
    // Vérifier si c'est une erreur
    if (isset($videoInfo['success']) && !$videoInfo['success']) {
        throw new Exception($videoInfo['error'] ?? 'Erreur inconnue');
    }
    
    // Formater les informations pour l'affichage
    $formattedInfo = [
        'success' => true,
        'id' => $videoInfo['id'] ?? $videoId,
        'title' => $videoInfo['title'] ?? 'Titre indisponible',
        'duration' => $videoInfo['duration'] ?? 'Inconnue',
        'thumbnail' => $videoInfo['thumbnail'] ?? '',
        'uploader' => $videoInfo['uploader'] ?? 'Inconnu',
        'view_count' => $videoInfo['view_count'] ?? null,
        'upload_date' => $videoInfo['upload_date'] ?? null,
        'formats' => []
    ];
    
    // Traiter les formats disponibles
    if (isset($videoInfo['formats']) && is_array($videoInfo['formats'])) {
        foreach ($videoInfo['formats'] as $format) {
            $formattedInfo['formats'][] = [
                'type' => $format['type'] ?? 'Format inconnu',
                'quality' => $format['quality'] ?? 'Qualité inconnue',
                'size' => $format['size'] ?? 'Taille inconnue'
            ];
        }
    } else {
        // Formats par défaut si non disponibles
        $formattedInfo['formats'] = [
            [
                'type' => 'Audio MP3',
                'quality' => '192 kbps',
                'size' => 'Calculé au téléchargement'
            ],
            [
                'type' => 'Vidéo MP4 480p',
                'quality' => '480p - 30 fps',
                'size' => 'Calculé au téléchargement'
            ],
            [
                'type' => 'Vidéo MP4 720p',
                'quality' => '720p - 30 fps',
                'size' => 'Calculé au téléchargement'
            ],
            [
                'type' => 'Vidéo MP4 1080p',
                'quality' => '1080p - 30 fps',
                'size' => 'Calculé au téléchargement'
            ]
        ];
    }
    
    // Log de l'activité
    error_log(sprintf(
        "[YTDownloader] Info request: %s | Video: %s | Title: %s | IP: %s",
        date('Y-m-d H:i:s'),
        $videoId,
        substr($formattedInfo['title'], 0, 50),
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ));
    
    // Retourner les informations formatées
    echo json_encode($formattedInfo, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("[YTDownloader] Info error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>