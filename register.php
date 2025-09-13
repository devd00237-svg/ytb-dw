<?php
require_once 'config.php';

$error = '';
$success = '';

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $typeCompte = 'gratuit'; // Force toujours le type gratuit lors de l'inscription
        $acceptTerms = isset($_POST['accept_terms']);
        
        // Validation
        if (empty($email) || empty($password) || empty($confirmPassword)) {
            $error = 'Veuillez remplir tous les champs obligatoires';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide';
        } elseif (strlen($password) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères';
        } elseif ($password !== $confirmPassword) {
            $error = 'Les mots de passe ne correspondent pas';
        } elseif (!$acceptTerms) {
            $error = 'Vous devez accepter les conditions d\'utilisation';
        } else {
            try {
                // Vérifier si l'email existe déjà
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $error = 'Cette adresse email est déjà utilisée';
                } else {
                    // Créer l'utilisateur
                    $userManager = new UserManager();
                    $userId = $userManager->createUser($email, $password, $typeCompte);
                    
                    if ($userId) {
                        // Mettre à jour les statistiques
                        $db->exec("UPDATE site_stats SET total_users = total_users + 1");
                        
                        // Connecter automatiquement l'utilisateur
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_type'] = $typeCompte;
                        
                        session_regenerate_id(true);
                        
                        $success = 'Compte créé avec succès ! Redirection en cours...';
                        header("refresh:2;url=dashboard.php");
                    } else {
                        $error = 'Erreur lors de la création du compte';
                    }
                }
            } catch (Exception $e) {
                $error = 'Erreur technique : ' . $e->getMessage();
            }
        }
    }
}

// Redirection si déjà connecté
if (isset($_SESSION['user_id']) && !$success) {
    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?= SITE_NAME ?></title>
    <meta name="description" content="Créez votre compte <?= SITE_NAME ?> pour obtenir votre clé API et accéder à tous nos services.">
    
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
        .strength-indicator {
            height: 4px;
            transition: all 0.3s ease;
        }
        .disabled-option {
            opacity: 0.6;
            cursor: not-allowed !important;
            position: relative;
        }
        .disabled-option::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
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
                    <i class="fas fa-user-plus text-purple-600 text-3xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-white">
                Créer votre compte
            </h2>
            <p class="mt-2 text-sm text-purple-100">
                Ou 
                <a href="login.php" class="font-medium text-white hover:text-purple-200 underline">
                    connectez-vous à votre compte existant
                </a>
            </p>
        </div>

        <!-- Formulaire d'inscription -->
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

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Adresse email *
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

                <!-- Type de compte -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-crown mr-2"></i>Type de compte
                    </label>
                    <div class="space-y-3">
                        <!-- Compte gratuit - sélectionnable -->
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input
                                type="radio"
                                name="type_compte"
                                value="gratuit"
                                class="text-purple-600 focus:ring-purple-500"
                                checked
                                readonly
                            >
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-900">Gratuit</span>
                                    <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Inscription</span>
                                </div>
                                <p class="text-sm text-gray-600">10 téléchargements/jour • Audio MP3 uniquement</p>
                            </div>
                        </label>

                        <!-- Compte premium - non sélectionnable -->
                        <div class="disabled-option flex items-center p-3 border border-gray-200 rounded-lg">
                            <input
                                type="radio"
                                disabled
                                class="text-gray-400"
                            >
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-600">Premium</span>
                                    <span class="ml-2 bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Après inscription</span>
                                </div>
                                <p class="text-sm text-gray-500">100 téléchargements/jour • Tous formats (MP3, MP4)</p>
                            </div>
                        </div>

                        <!-- Compte unlimited - non sélectionnable -->
                        <div class="disabled-option flex items-center p-3 border border-gray-200 rounded-lg">
                            <input
                                type="radio"
                                disabled
                                class="text-gray-400"
                            >
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-600">Unlimited</span>
                                    <span class="ml-2 bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Après inscription</span>
                                </div>
                                <p class="text-sm text-gray-500">Téléchargements illimités • Tous formats • Support prioritaire</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Information sur la mise à niveau -->
                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-700">
                                <strong>Mise à niveau disponible :</strong> Vous pourrez passer à un compte Premium ou Unlimited depuis votre dashboard après inscription.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Mot de passe *
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            minlength="8"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 pr-10"
                            placeholder="Minimum 8 caractères"
                        >
                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <!-- Indicateur de force du mot de passe -->
                    <div class="mt-2">
                        <div id="password-strength" class="strength-indicator bg-gray-200 rounded-full"></div>
                        <p id="password-strength-text" class="text-xs text-gray-500 mt-1"></p>
                    </div>
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Confirmer le mot de passe *
                    </label>
                    <div class="relative">
                        <input
                            id="confirm_password"
                            name="confirm_password"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 pr-10"
                            placeholder="Répétez votre mot de passe"
                        >
                        <div id="password-match" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-times text-red-500 hidden"></i>
                            <i class="fas fa-check text-green-500 hidden"></i>
                        </div>
                    </div>
                </div>

                <!-- Conditions d'utilisation -->
                <div class="flex items-start">
                    <input
                        id="accept_terms"
                        name="accept_terms"
                        type="checkbox"
                        required
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded mt-0.5"
                    >
                    <label for="accept_terms" class="ml-2 block text-sm text-gray-700">
                        J'accepte les 
                        <a href="mentions.php" class="font-medium text-purple-600 hover:text-purple-500">conditions d'utilisation</a> 
                        et la 
                        <a href="privacy.php" class="font-medium text-purple-600 hover:text-purple-500">politique de confidentialité</a> *
                    </label>
                </div>

                <!-- Newsletter -->
                <div class="flex items-center">
                    <input
                        id="newsletter"
                        name="newsletter"
                        type="checkbox"
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                    >
                    <label for="newsletter" class="ml-2 block text-sm text-gray-700">
                        Je souhaite recevoir les actualités et offres spéciales par email
                    </label>
                </div>

                <!-- Bouton d'inscription -->
                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submit-btn"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-purple-300 group-hover:text-purple-200"></i>
                        </span>
                        Créer mon compte gratuit
                    </button>
                </div>
            </form>

            <!-- Avantages -->
            <div class="mt-8 bg-purple-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-purple-800 mb-3">
                    <i class="fas fa-gift mr-2"></i>Ce que vous obtenez
                </h3>
                <ul class="text-xs text-purple-700 space-y-1">
                    <li><i class="fas fa-check mr-2"></i>Clé API générée automatiquement</li>
                    <li><i class="fas fa-check mr-2"></i>Dashboard de gestion complet</li>
                    <li><i class="fas fa-check mr-2"></i>Documentation technique détaillée</li>
                    <li><i class="fas fa-check mr-2"></i>Historique des téléchargements</li>
                    <li><i class="fas fa-check mr-2"></i>Option de mise à niveau Premium/Unlimited</li>
                    <li><i class="fas fa-check mr-2"></i>Support technique dédié</li>
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

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('password-strength-text');
            
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch (strength) {
                case 0:
                case 1:
                    strengthBar.className = 'strength-indicator bg-red-500 rounded-full';
                    strengthBar.style.width = '20%';
                    feedback = 'Très faible';
                    break;
                case 2:
                    strengthBar.className = 'strength-indicator bg-orange-500 rounded-full';
                    strengthBar.style.width = '40%';
                    feedback = 'Faible';
                    break;
                case 3:
                    strengthBar.className = 'strength-indicator bg-yellow-500 rounded-full';
                    strengthBar.style.width = '60%';
                    feedback = 'Moyen';
                    break;
                case 4:
                    strengthBar.className = 'strength-indicator bg-blue-500 rounded-full';
                    strengthBar.style.width = '80%';
                    feedback = 'Fort';
                    break;
                case 5:
                    strengthBar.className = 'strength-indicator bg-green-500 rounded-full';
                    strengthBar.style.width = '100%';
                    feedback = 'Très fort';
                    break;
            }
            
            strengthText.textContent = feedback;
        });

        // Password confirmation check
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('password-match');
            const checkIcon = matchDiv.querySelector('.fa-check');
            const timesIcon = matchDiv.querySelector('.fa-times');
            
            if (confirmPassword.length === 0) {
                checkIcon.classList.add('hidden');
                timesIcon.classList.add('hidden');
                return;
            }
            
            if (password === confirmPassword) {
                checkIcon.classList.remove('hidden');
                timesIcon.classList.add('hidden');
            } else {
                checkIcon.classList.add('hidden');
                timesIcon.classList.remove('hidden');
            }
        }

        document.getElementById('password').addEventListener('input', checkPasswordMatch);
        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const acceptTerms = document.getElementById('accept_terms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas');
                return;
            }
            
            if (!acceptTerms) {
                e.preventDefault();
                alert('Vous devez accepter les conditions d\'utilisation');
                return;
            }
        });

        // Empêcher la sélection des options désactivées
        document.querySelectorAll('.disabled-option').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
        });
    </script>
</body>
</html>