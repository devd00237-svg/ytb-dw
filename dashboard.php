<?php
require_once 'config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$userManager = new UserManager();
$db = Database::getInstance()->getConnection();

// Récupérer les informations de l'utilisateur
$stmt = $db->prepare("
    SELECT u.*, 
           CASE 
               WHEN u.last_quota_reset < CURDATE() THEN 0 
               ELSE u.daily_quota_used 
           END as current_quota_used
    FROM users u 
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    redirect('login.php');
}

// Récupérer les clés API
$stmt = $db->prepare("
    SELECT * FROM user_api_keys 
    WHERE user_id = ? AND active = 1 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$apiKeys = $stmt->fetchAll();

// Récupérer les téléchargements récents
$stmt = $db->prepare("
    SELECT d.*, 
           DATE_FORMAT(d.download_date, '%d/%m/%Y à %H:%i') as formatted_date
    FROM downloads d 
    WHERE d.user_id = ? 
    ORDER BY d.download_date DESC 
    LIMIT 20
");
$stmt->execute([$_SESSION['user_id']]);
$recentDownloads = $stmt->fetchAll();

// Statistiques de l'utilisateur
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_downloads,
        COUNT(CASE WHEN DATE(download_date) = CURDATE() THEN 1 END) as today_downloads,
        COUNT(CASE WHEN download_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_downloads,
        COUNT(CASE WHEN download_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as month_downloads
    FROM downloads 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$userStats = $stmt->fetch();

// Définir les quotas selon le type de compte
$quotaLimits = [
    'gratuit' => QUOTA_GRATUIT,
    'premium' => QUOTA_PREMIUM,
    'unlimited' => QUOTA_UNLIMITED
];

$currentQuotaLimit = $quotaLimits[$user['type_compte']] ?? QUOTA_GRATUIT;
$quotaUsed = $user['current_quota_used'];
$quotaRemaining = $currentQuotaLimit > 0 ? max(0, $currentQuotaLimit - $quotaUsed) : 'Illimité';

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create_api_key':
                $keyName = trim($_POST['key_name'] ?? 'Nouvelle clé');
                if (!empty($keyName)) {
                    $newApiKey = $userManager->createAPIKey($_SESSION['user_id'], $keyName);
                    $success = 'Nouvelle clé API créée avec succès !';
                    // Recharger la page pour afficher la nouvelle clé
                    header("Location: dashboard.php");
                    exit;
                }
                break;
                
            case 'deactivate_api_key':
                $keyId = (int)($_POST['key_id'] ?? 0);
                $stmt = $db->prepare("
                    UPDATE user_api_keys 
                    SET active = 0 
                    WHERE id = ? AND user_id = ?
                ");
                $stmt->execute([$keyId, $_SESSION['user_id']]);
                $success = 'Clé API désactivée avec succès';
                header("Location: dashboard.php");
                exit;
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= SITE_NAME ?></title>
    <meta name="description" content="Gérez vos clés API, consultez vos statistiques et historique de téléchargements.">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .quota-progress {
            transition: width 0.3s ease;
        }
        
        /* Header fixe */
        .fixed-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        /* Ajuster le body pour le header fixe */
        body {
            padding-top: 72px; /* Hauteur approximative du header */
        }
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            body {
                padding-top: 64px; /* Hauteur réduite sur mobile */
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation fixe -->
    <nav class="fixed-nav">
        <div class="container mx-auto px-4 sm:px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="fas fa-download text-purple-600 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800"><?= SITE_NAME ?></span>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-purple-600 transition-colors">Accueil</a>
                    <a href="docs.php" class="text-gray-700 hover:text-purple-600 transition-colors">Documentation</a>
                    <div class="flex items-center space-x-2 text-gray-700">
                        <a href="profile.php">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-purple-600 text-sm"></i>
                            </div>
                        </a>
                        <a href="profile.php">
                            <span class="font-medium"><?= e($user['email']) ?></span>
                        </a>
                        <div class="px-2 py-1 bg-<?= $user['type_compte'] === 'gratuit' ? 'green' : ($user['type_compte'] === 'premium' ? 'purple' : 'yellow') ?>-100 text-<?= $user['type_compte'] === 'gratuit' ? 'green' : ($user['type_compte'] === 'premium' ? 'purple' : 'yellow') ?>-800 text-xs rounded-full">
                            <?= ucfirst($user['type_compte']) ?>
                        </div>
                    </div>
                    <a href="logout.php" class="text-red-600 hover:text-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700 hover:text-purple-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menu mobile -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-4 sm:px-6 py-3 space-y-2">
                <a href="index.php" class="block text-gray-700 py-2">Accueil</a>
                <a href="docs.php" class="block text-gray-700 py-2">Documentation</a>
                <div class="flex items-center py-2">
                    <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-user text-purple-600 text-xs"></i>
                    </div>
                    <span class="text-sm text-gray-700"><?= e($user['email']) ?></span>
                </div>
                <a href="logout.php" class="block text-red-600 py-2">Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Header Dashboard -->
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Dashboard</h1>
                    <p class="text-purple-100">Bienvenue <?= e($user['email']) ?> • Compte <?= ucfirst($user['type_compte']) ?></p>
                </div>
                <div class="text-right">
                    <p class="text-purple-100">Membre depuis</p>
                    <p class="text-white font-semibold"><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container mx-auto px-6 py-8">
        <!-- Messages -->
        <?php if (isset($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-download text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?= $userStats['total_downloads'] ?></h3>
                        <p class="text-gray-600 text-sm">Total téléchargements</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?= $userStats['today_downloads'] ?></h3>
                        <p class="text-gray-600 text-sm">Aujourd'hui</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-calendar-week text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?= $userStats['week_downloads'] ?></h3>
                        <p class="text-gray-600 text-sm">Cette semaine</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?= $userStats['month_downloads'] ?></h3>
                        <p class="text-gray-600 text-sm">Ce mois</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quota journalier -->
        <?php if ($currentQuotaLimit > 0): ?>
            <div class="bg-white rounded-lg p-6 shadow-lg mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-pie mr-2 text-purple-600"></i>Quota journalier
                </h2>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">
                        <?= $quotaUsed ?> / <?= $currentQuotaLimit ?> téléchargements utilisés
                    </span>
                    <span class="text-sm text-gray-500">
                        <?= $quotaRemaining ?> restants
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="quota-progress bg-purple-600 h-2 rounded-full" style="width: <?= min(100, ($quotaUsed / $currentQuotaLimit) * 100) ?>%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Le quota se remet à zéro chaque jour à minuit
                </p>
            </div>
        <?php else: ?>
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-200">
                        <i class="fas fa-infinity text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-yellow-800">Téléchargements illimités</h2>
                        <p class="text-yellow-700">Votre compte Unlimited vous permet des téléchargements sans restriction</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Gestion des clés API -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-key mr-2 text-purple-600"></i>Clés API
                    </h2>
                    <button 
                        onclick="openCreateKeyModal()"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm"
                    >
                        <i class="fas fa-plus mr-2"></i>Nouvelle clé
                    </button>
                </div>

                <?php if (empty($apiKeys)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-key text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Aucune clé API trouvée</p>
                        <p class="text-gray-400 text-sm">Créez votre première clé pour commencer</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($apiKeys as $key): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h3 class="font-medium text-gray-800"><?= e($key['key_name']) ?></h3>
                                        <p class="text-xs text-gray-500">
                                            Créée le <?= date('d/m/Y', strtotime($key['created_at'])) ?>
                                            <?php if ($key['last_used_at']): ?>
                                                • Dernière utilisation : <?= date('d/m/Y à H:i', strtotime($key['last_used_at'])) ?>
                                            <?php else: ?>
                                                • Jamais utilisée
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            onclick="copyApiKey('<?= e($key['api_key']) ?>')"
                                            class="text-blue-600 hover:text-blue-800 text-sm"
                                            title="Copier la clé"
                                        >
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button
                                            onclick="deactivateApiKey(<?= $key['id'] ?>, '<?= e($key['key_name']) ?>')"
                                            class="text-red-600 hover:text-red-800 text-sm"
                                            title="Désactiver"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded p-3 font-mono text-sm break-all">
                                    <span id="key-<?= $key['id'] ?>" class="select-all"><?= e($key['api_key']) ?></span>
                                </div>
                                <div class="mt-2 text-xs text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Formats autorisés : 
                                    <?php 
                                    $allowedFormats = match($user['type_compte']) {
                                        'gratuit' => 'Audio MP3 uniquement',
                                        'premium', 'unlimited' => 'Audio MP3, Vidéo MP4 (toutes qualités)',
                                        default => 'Audio MP3 uniquement'
                                    };
                                    echo $allowedFormats;
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Exemple d'utilisation -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium text-gray-800 mb-2">
                        <i class="fas fa-code mr-2"></i>Exemple d'utilisation
                    </h3>
                    <pre class="text-xs bg-gray-800 text-green-400 p-3 rounded overflow-x-auto"><code>GET <?= SITE_URL ?>/download.php?api_key=VOTRE_CLE&url=https://youtube.com/watch?v=VIDEO_ID&format=video&quality=720</code></pre>
                </div>
            </div>

            <!-- Historique des téléchargements -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-history mr-2 text-purple-600"></i>Historique récent
                </h2>

                <?php if (empty($recentDownloads)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-download text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Aucun téléchargement récent</p>
                        <p class="text-gray-400 text-sm">Vos téléchargements apparaîtront ici</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php foreach ($recentDownloads as $download): ?>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-<?= $download['format'] === 'audio' ? 'file-audio' : 'file-video' ?> text-<?= $download['format'] === 'audio' ? 'green' : 'blue' ?>-600"></i>
                                        <span class="text-sm font-medium text-gray-800 truncate">
                                            <?= e($download['video_title'] ?: 'Titre indisponible') ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                                        <span>
                                            <i class="fas fa-<?= $download['format'] === 'audio' ? 'music' : 'video' ?> mr-1"></i>
                                            <?= ucfirst($download['format']) ?>
                                            <?php if ($download['quality']): ?>
                                                • <?= e($download['quality']) ?>p
                                            <?php endif; ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            <?= $download['formatted_date'] ?>
                                        </span>
                                        <?php if ($download['file_size']): ?>
                                            <span>
                                                <i class="fas fa-weight mr-1"></i>
                                                <?= formatFileSize($download['file_size']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a 
                                        href="https://youtube.com/watch?v=<?= e($download['video_id']) ?>" 
                                        target="_blank"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                        title="Voir sur YouTube"
                                    >
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($recentDownloads) >= 20): ?>
                        <div class="mt-4 text-center">
                            <a href="history.php" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                Voir l'historique complet <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section upgrade de compte -->
        <?php if ($user['type_compte'] === 'gratuit'): ?>
            <div class="mt-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">Passez au Premium !</h2>
                        <p class="text-purple-100 mb-4">
                            Débloquez tous les formats vidéo et augmentez votre quota quotidien
                        </p>
                        <ul class="text-sm text-purple-100 space-y-1">
                            <li><i class="fas fa-check mr-2"></i>100 téléchargements par jour (au lieu de 10)</li>
                            <li><i class="fas fa-check mr-2"></i>Tous les formats : MP3, MP4 480p, 720p, 1080p</li>
                            <li><i class="fas fa-check mr-2"></i>Support prioritaire</li>
                            <li><i class="fas fa-check mr-2"></i>API avancée avec webhooks</li>
                        </ul>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2">9,99€</div>
                        <div class="text-purple-200 text-sm mb-4">par mois</div>
                        <button class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            Upgrade maintenant
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Accès rapide -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-rocket mr-2 text-purple-600"></i>Accès rapide
            </h2>
            <div class="grid md:grid-cols-3 gap-4">
                <a href="index.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-download text-blue-600 text-xl mr-3"></i>
                    <div>
                        <div class="font-medium text-blue-800">Télécharger</div>
                        <div class="text-sm text-blue-600">Nouveau téléchargement</div>
                    </div>
                </a>
                
                <a href="docs.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-book text-green-600 text-xl mr-3"></i>
                    <div>
                        <div class="font-medium text-green-800">Documentation</div>
                        <div class="text-sm text-green-600">Guide API</div>
                    </div>
                </a>
                
                <a href="profile.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-user-cog text-purple-600 text-xl mr-3"></i>
                    <div>
                        <div class="font-medium text-purple-800">Profil</div>
                        <div class="text-sm text-purple-600">Gérer le compte</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal création de clé API -->
    <div id="createKeyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Créer une nouvelle clé API</h3>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="create_api_key">
                
                <div class="mb-4">
                    <label for="key_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la clé
                    </label>
                    <input
                        type="text"
                        id="key_name"
                        name="key_name"
                        placeholder="Ex: Mon application mobile"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Donnez un nom descriptif à votre clé pour la retrouver facilement</p>
                </div>
                
                <div class="flex space-x-3">
                    <button 
                        type="button"
                        onclick="closeCreateKeyModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
                    >
                        Créer la clé
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal confirmation suppression -->
    <div id="deleteKeyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-800">Confirmer la suppression</h3>
            </div>
            
            <p class="text-gray-600 mb-6">
                Êtes-vous sûr de vouloir désactiver la clé "<span id="deleteKeyName"></span>" ?
                Cette action est irréversible.
            </p>
            
            <form method="POST" id="deleteKeyForm">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="deactivate_api_key">
                <input type="hidden" name="key_id" id="deleteKeyId">
                
                <div class="flex space-x-3">
                    <button 
                        type="button"
                        onclick="closeDeleteKeyModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Désactiver
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Menu mobile toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Gestion des modals
        function openCreateKeyModal() {
            document.getElementById('createKeyModal').classList.remove('hidden');
            document.getElementById('key_name').focus();
        }

        function closeCreateKeyModal() {
            document.getElementById('createKeyModal').classList.add('hidden');
        }

        function deactivateApiKey(keyId, keyName) {
            document.getElementById('deleteKeyId').value = keyId;
            document.getElementById('deleteKeyName').textContent = keyName;
            document.getElementById('deleteKeyModal').classList.remove('hidden');
        }

        function closeDeleteKeyModal() {
            document.getElementById('deleteKeyModal').classList.add('hidden');
        }

        // Copier clé API
        function copyApiKey(apiKey) {
            navigator.clipboard.writeText(apiKey).then(() => {
                // Afficher une notification temporaire
                const notification = document.createElement('div');
                notification.className = 'fixed top-20 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                notification.textContent = 'Clé API copiée !';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }).catch(() => {
                alert('Erreur lors de la copie. Sélectionnez et copiez manuellement.');
            });
        }

        // Fermer les modals en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (e.target.id === 'createKeyModal') {
                closeCreateKeyModal();
            }
            if (e.target.id === 'deleteKeyModal') {
                closeDeleteKeyModal();
            }
        });

        // Fermer les modals avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateKeyModal();
                closeDeleteKeyModal();
            }
        });
    </script>
</body>
</html>