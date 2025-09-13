<?php
require_once 'config.php';

$error = '';
$success = '';

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Veuillez remplir tous les champs';
        } else {
            $userManager = new UserManager();
            $user = $userManager->authenticate($email, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_type'] = $user['type_compte'];
                
                // Régénérer l'ID de session pour la sécurité
                session_regenerate_id(true);
                
                redirect('dashboard.php');
            } else {
                $error = 'Email ou mot de passe incorrect';
            }
        }
    }
}

// Redirection si déjà connecté
if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?= SITE_NAME ?></title>
    <meta name="description" content="Connectez-vous à votre compte <?= SITE_NAME ?> pour accéder à votre dashboard et gérer vos clés API.">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="index.php" class="inline-flex items-center text-white hover:text-purple-200 mb-8">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à l'accueil
            </a>
            
            <div class="flex justify-center">
                <div class="bg-white rounded-full p-3 shadow-lg">
                    <i class="fas fa-download text-purple-600 text-3xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-white">
                Connexion à votre compte
            </h2>
            <p class="mt-2 text-sm text-purple-100">
                Ou 
                <a href="register.php" class="font-medium text-white hover:text-purple-200 underline">
                    créez un nouveau compte
                </a>
            </p>
        </div>

        <!-- Formulaire de connexion -->
        <div class="glass-effect rounded-xl shadow-xl p-8">
            <?php if ($error): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?= e($error) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= e($success) ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Adresse email
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="<?= e($_POST['email'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        placeholder="votre@email.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Mot de passe
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 pr-10"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember-me"
                            name="remember-me"
                            type="checkbox"
                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        >
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="forgot-password.php" class="font-medium text-purple-600 hover:text-purple-500">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-purple-300 group-hover:text-purple-200"></i>
                        </span>
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Liens supplémentaires -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="text-center space-y-2">
                    <p class="text-sm text-gray-600">
                        Vous n'avez pas encore de compte ?
                    </p>
                    <a 
                        href="register.php" 
                        class="inline-flex items-center text-purple-600 hover:text-purple-500 font-medium"
                    >
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer un compte gratuit
                    </a>
                </div>
            </div>

            <!-- Avantages du compte -->
            <div class="mt-8 bg-purple-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-purple-800 mb-3">
                    <i class="fas fa-star mr-2"></i>Avantages du compte
                </h3>
                <ul class="text-xs text-purple-700 space-y-1">
                    <li><i class="fas fa-check mr-2"></i>Clé API automatique</li>
                    <li><i class="fas fa-check mr-2"></i>Dashboard de gestion</li>
                    <li><i class="fas fa-check mr-2"></i>Suivi des téléchargements</li>
                    <li><i class="fas fa-check mr-2"></i>Documentation complète</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-sm text-purple-100">
                &copy; <?= date('Y') ?> <?= SITE_NAME ?>. Tous droits réservés.
            </p>
            <div class="mt-2 space-x-4 text-xs">
                <a href="#" class="text-purple-200 hover:text-white">CGU</a>
                <a href="#" class="text-purple-200 hover:text-white">Confidentialité</a>
                <a href="#" class="text-purple-200 hover:text-white">Support</a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Auto-focus sur le premier champ vide
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            
            if (!emailField.value) {
                emailField.focus();
            } else if (!passwordField.value) {
                passwordField.focus();
            }
        });
    </script>
</body>
</html>