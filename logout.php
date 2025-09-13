<?php
require_once 'config.php';

/**
 * Gestionnaire de déconnexion sécurisée
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur était connecté
$wasLoggedIn = isset($_SESSION['user_id']);
$userEmail = $_SESSION['user_email'] ?? 'Utilisateur';

// Détruire toutes les données de session
$_SESSION = array();

// Supprimer le cookie de session si il existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Log de la déconnexion
if ($wasLoggedIn) {
    error_log(sprintf(
        "[YTDownloader] Logout: %s | User: %s | IP: %s",
        date('Y-m-d H:i:s'),
        $userEmail,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ));
}

// Redirection vers la page d'accueil avec message
session_start();
$_SESSION['logout_message'] = 'Vous avez été déconnecté avec succès.';
redirect('index.php');
?>