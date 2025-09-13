<?php
/**
 * Configuration globale du SaaS YouTube Downloader
 */

// Configuration de l'API Node.js
define('NODE_API_URL', 'https://web-production-460cb.up.railway.app');

// Configuration de la base de données
define('DB_HOST', 'sql303.byethost7.com');
define('DB_NAME', 'b7_39935245_ytb_dw');
define('DB_USER', 'b7_39935245');
define('DB_PASS', ';*Azerty16.com');

// Configuration du site
define('SITE_NAME', 'YTB-Downloader Pro');
define('SITE_URL', 'https://ytb-dw.social-networking.me');
define('SITE_EMAIL', 'contact@votredomaine.com');

// Configuration des quotas
define('QUOTA_GRATUIT', 10);
define('QUOTA_PREMIUM', 100);
define('QUOTA_UNLIMITED', -1); // -1 = illimité

// Configuration de sécurité
define('SESSION_LIFETIME', 7200); // 2 heures
define('API_KEY_PREFIX', 'ytb-dw-');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Formats autorisés par type de compte
define('FORMATS_VISITOR', ['audio', 'video']); // Visiteurs : tous formats
define('FORMATS_GRATUIT', ['audio']); // Gratuit : audio uniquement
define('FORMATS_PREMIUM', ['audio', 'video']); // Premium : tous formats
define('FORMATS_UNLIMITED', ['audio', 'video']); // Unlimited : tous formats

// Qualités vidéo disponibles
define('VIDEO_QUALITIES', ['480', '720', '1080', 'best']);

// Timezone
date_default_timezone_set('Europe/Paris');

// Configuration des erreurs (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Classe de connexion à la base de données
 */
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}

/**
 * Fonctions utilitaires globales
 */

/**
 * Génère un token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Vérifie un token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Génère une clé API unique
 */
function generateAPIKey() {
    return API_KEY_PREFIX . bin2hex(random_bytes(16));
}

/**
 * Valide une URL YouTube
 */
function isValidYouTubeURL($url) {
    return preg_match('/^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]+/', $url);
}

/**
 * Extrait l'ID d'une vidéo YouTube
 */
function extractYouTubeID($url) {
    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

/**
 * Formate une taille de fichier
 */
function formatFileSize($bytes) {
    if (!$bytes) return 'Inconnue';
    $units = ['o', 'Ko', 'Mo', 'Go'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
}

/**
 * Escape HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige vers une URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Démarre une session sécurisée
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Classe de gestion des utilisateurs
 */
class UserManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function createUser($email, $password, $type_compte = 'gratuit') {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password_hash, type_compte) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $hashedPassword, $type_compte]);
        
        $userId = $this->db->lastInsertId();
        
        // Créer une clé API automatiquement
        $this->createAPIKey($userId);
        
        return $userId;
    }

    /**
     * Authentifie un utilisateur
     */
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("
            SELECT id, password_hash, type_compte 
            FROM users 
            WHERE email = ? AND is_active = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    /**
     * Crée une clé API pour un utilisateur
     */
    public function createAPIKey($userId, $keyName = 'Clé principale') {
        $apiKey = generateAPIKey();
        
        $stmt = $this->db->prepare("
            INSERT INTO user_api_keys (user_id, api_key, key_name) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $apiKey, $keyName]);
        
        return $apiKey;
    }

    /**
     * Valide une clé API
     */
    public function validateAPIKey($apiKey) {
        $stmt = $this->db->prepare("
            SELECT uak.*, u.type_compte, u.daily_quota_used, u.last_quota_reset
            FROM user_api_keys uak
            JOIN users u ON uak.user_id = u.id
            WHERE uak.api_key = ? AND uak.active = 1 AND u.is_active = 1
        ");
        $stmt->execute([$apiKey]);
        return $stmt->fetch();
    }

    /**
     * Met à jour l'utilisation d'une clé API
     */
    public function updateAPIKeyUsage($apiKey) {
        $stmt = $this->db->prepare("
            UPDATE user_api_keys 
            SET last_used_at = CURRENT_TIMESTAMP 
            WHERE api_key = ?
        ");
        $stmt->execute([$apiKey]);
    }

    /**
     * Vérifie et met à jour le quota quotidien
     */
    public function checkAndUpdateQuota($userId, $typeCompte) {
        // Réinitialiser le quota si nécessaire
        $stmt = $this->db->prepare("
            UPDATE users 
            SET daily_quota_used = 0, last_quota_reset = CURDATE()
            WHERE id = ? AND last_quota_reset < CURDATE()
        ");
        $stmt->execute([$userId]);

        // Obtenir le quota actuel
        $stmt = $this->db->prepare("
            SELECT daily_quota_used FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $quotaUsed = $stmt->fetchColumn();

        // Définir les limites selon le type de compte
        $quotaLimit = match($typeCompte) {
            'gratuit' => QUOTA_GRATUIT,
            'premium' => QUOTA_PREMIUM,
            'unlimited' => QUOTA_UNLIMITED,
            default => QUOTA_GRATUIT
        };

        // Vérifier si le quota est dépassé
        if ($quotaLimit > 0 && $quotaUsed >= $quotaLimit) {
            return false;
        }

        // Incrémenter le quota utilisé
        $stmt = $this->db->prepare("
            UPDATE users 
            SET daily_quota_used = daily_quota_used + 1 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);

        return true;
    }
}

// Initialiser la session
startSecureSession();
?>
