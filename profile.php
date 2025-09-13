<?php
require_once 'config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$userManager = new UserManager();
$db = Database::getInstance()->getConnection();

// Récupérer les informations de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    redirect('login.php');
}

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'update_profile':
                $email = trim($_POST['email'] ?? '');
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Adresse email invalide';
                } else {
                    // Vérifier si l'email n'est pas déjà utilisé
                    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$email, $_SESSION['user_id']]);
                    
                    if ($stmt->fetch()) {
                        $error = 'Cette adresse email est déjà utilisée';
                    } else {
                        // Mettre à jour l'email
                        $stmt = $db->prepare("UPDATE users SET email = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$email, $_SESSION['user_id']]);
                        
                        $user['email'] = $email;
                        $success = 'Profil mis à jour avec succès !';
                    }
                }
                break;
                
            case 'change_password':
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (empty($currentPassword)) {
                    $error = 'Le mot de passe actuel est requis';
                } elseif (!password_verify($currentPassword, $user['password_hash'])) {
                    $error = 'Mot de passe actuel incorrect';
                } elseif (strlen($newPassword) < 8) {
                    $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Les mots de passe ne correspondent pas';
                } else {
                    // Changer le mot de passe
                    $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
                    $stmt = $db->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$passwordHash, $_SESSION['user_id']]);
                    
                    $success = 'Mot de passe changé avec succès !';
                }
                break;
                
            case 'delete_account':
                $password = $_POST['password'] ?? '';
                
                if (!password_verify($password, $user['password_hash'])) {
                    $error = 'Mot de passe incorrect';
                } else {
                    // Désactiver le compte au lieu de le supprimer
                    $stmt = $db->prepare("UPDATE users SET is_active = 0, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    
                    // Désactiver toutes les clés API
                    $stmt = $db->prepare("UPDATE user_api_keys SET active = 0 WHERE user_id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    
                    // Déconnecter l'utilisateur
                    session_destroy();
                    redirect('index.php?deleted=1');
                }
                break;
        }
    }
}

// Statistiques du compte
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_downloads,
        COUNT(DISTINCT DATE(download_date)) as active_days,
        MIN(download_date) as first_download,
        MAX(download_date) as last_download
    FROM downloads 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$accountStats = $stmt->fetch();

// Nombre de clés API actives
$stmt = $db->prepare("SELECT COUNT(*) as active_keys FROM user_api_keys WHERE user_id = ? AND active = 1");
$stmt->execute([$_SESSION['user_id']]);
$apiKeysCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?= SITE_NAME ?></title>
    <meta name="description" content="Gérez votre profil, modifiez vos informations personnelles et paramètres de compte.">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .fixed-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.1); transition: all 0.3s ease;
        }
        body { padding-top: 72px; }
        @media (max-width: 1024px) { body { padding-top: 64px; } }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed-nav">
        <div class="container mx-auto px-4 sm:px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="fas fa-download text-purple-600 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800"><?= SITE_NAME ?></span>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-purple-600 transition-colors">Accueil</a>
                    <a href="dashboard.php" class="text-gray-700 hover:text-purple-600 transition-colors">Dashboard</a>
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
    </nav>

    <!-- Header -->
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Mon Profil</h1>
                    <p class="text-purple-100">Gérez vos informations personnelles et paramètres</p>
                </div>
                <div class="text-right">
                    <div class="px-4 py-2 bg-white bg-opacity-20 rounded-lg">
                        <p class="text-purple-100 text-sm">Compte</p>
                        <p class="text-white font-semibold"><?= ucfirst($user['type_compte']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        <!-- Messages -->
        <?php if (isset($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= e($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i><?= e($success) ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques du compte -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800"><?= date('d/m/Y', strtotime($user['created_at'])) ?></h3>
                        <p class="text-gray-600 text-sm">Membre depuis</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-download text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800"><?= $accountStats['total_downloads'] ?: 0 ?></h3>
                        <p class="text-gray-600 text-sm">Téléchargements total</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-key text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800"><?= $apiKeysCount ?></h3>
                        <p class="text-gray-600 text-sm">Clés API actives</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <i class="fas fa-chart-line text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800"><?= $accountStats['active_days'] ?: 0 ?></h3>
                        <p class="text-gray-600 text-sm">Jours d'activité</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-user-edit mr-2 text-purple-600"></i>Informations personnelles
                </h2>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= e($user['email']) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                            required
                        >
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2">Informations du compte</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Type de compte:</span>
                                <span class="font-medium ml-2 px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                    <?= ucfirst($user['type_compte']) ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Statut:</span>
                                <span class="font-medium ml-2 text-green-600">
                                    <?= $user['is_active'] ? 'Actif' : 'Inactif' ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Créé le:</span>
                                <span class="font-medium ml-2"><?= date('d/m/Y à H:i', strtotime($user['created_at'])) ?></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Modifié le:</span>
                                <span class="font-medium ml-2"><?= date('d/m/Y à H:i', strtotime($user['updated_at'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                    </button>
                </form>
            </div>

            <!-- Changer le mot de passe -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-lock mr-2 text-purple-600"></i>Changer le mot de passe
                </h2>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe actuel
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                            required
                        >
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nouveau mot de passe
                        </label>
                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                            minlength="8"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le nouveau mot de passe
                        </label>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                            required
                        >
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-key mr-2"></i>Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>

        <!-- Zone dangereuse -->
        <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-red-800 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>Zone dangereuse
            </h2>
            <p class="text-red-700 mb-4">
                Les actions ci-dessous sont irréversibles. Veuillez procéder avec prudence.
            </p>
            
            <button 
                onclick="openDeleteModal()"
                class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors"
            >
                <i class="fas fa-user-times mr-2"></i>Supprimer mon compte
            </button>
        </div>

        <!-- Liens utiles -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-external-link-alt mr-2 text-purple-600"></i>Liens utiles
            </h2>
            <div class="grid md:grid-cols-3 gap-4">
                <a href="dashboard.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-tachometer-alt text-purple-600 text-xl mr-3"></i>
                    <div>
                        <div class="font-medium text-purple-800">Dashboard</div>
                        <div class="text-sm text-purple-600">Vue d'ensemble</div>
                    </div>
                </a>
                
                <a href="docs.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-book text-blue-600 text-xl mr-3"></i>
                    <div>
                        <div class="font-medium text-blue-800">Documentation</div>
                        <div class="text-sm text-blue-600">Guide API</div>
                    </div>
                </a>
                
                <a href="index.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-download text-green-600 text-xl mr-3"></i>
                    <div>
                        <div class="font-medium text-green-800">Télécharger</div>
                        <div class="text-sm text-green-600">Nouveau téléchargement</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal suppression de compte -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-800">Supprimer le compte</h3>
            </div>
            
            <p class="text-gray-600 mb-6">
                Cette action désactivera votre compte de manière permanente. Vos données seront conservées mais votre compte sera inaccessible.
            </p>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="delete_account">
                
                <div class="mb-4">
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmez avec votre mot de passe
                    </label>
                    <input
                        type="password"
                        id="delete_password"
                        name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                        required
                    >
                </div>
                
                <div class="flex space-x-3">
                    <button 
                        type="button"
                        onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Menu mobile
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu?.classList.toggle('hidden');
        });

        // Gestion du modal de suppression
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('delete_password').focus();
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('delete_password').value = '';
        }

        // Fermer modal en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (e.target.id === 'deleteModal') closeDeleteModal();
        });

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });

        // Validation du mot de passe en temps réel
        document.getElementById('new_password')?.addEventListener('input', function() {
            const confirmField = document.getElementById('confirm_password');
            if (confirmField.value && this.value !== confirmField.value) {
                confirmField.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                confirmField.setCustomValidity('');
            }
        });

        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            if (this.value !== newPassword) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>