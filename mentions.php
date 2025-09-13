<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Mentions Légales</title>
    <meta name="description" content="Mentions légales de <?= SITE_NAME ?>, service professionnel de téléchargement YouTube.">
    
    <!-- CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS personnalisé -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            padding-top: 72px;
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 64px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation fixe -->
    <nav class="fixed-nav">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="fas fa-download text-purple-600 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800"><?= SITE_NAME ?></span>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-purple-600 transition-colors">Accueil</a>
                    <a href="docs.php" class="text-gray-700 hover:text-purple-600 transition-colors">Documentation</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="text-gray-700 hover:text-purple-600 transition-colors">Dashboard</a>
                        <a href="logout.php" class="text-gray-700 hover:text-purple-600 transition-colors">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-700 hover:text-purple-600 transition-colors">Connexion</a>
                        <a href="register.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">Inscription</a>
                    <?php endif; ?>
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
            <div class="px-6 py-3 space-y-2">
                <a href="index.php" class="block text-gray-700 py-2">Accueil</a>
                <a href="docs.php" class="block text-gray-700 py-2">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="block text-gray-700 py-2">Dashboard</a>
                    <a href="logout.php" class="block text-gray-700 py-2">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="block text-gray-700 py-2">Connexion</a>
                    <a href="register.php" class="block text-gray-700 py-2">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Mentions
                <span class="block text-purple-200">Légales</span>
            </h1>
            <p class="text-xl mb-8 text-purple-100 max-w-3xl mx-auto">
                Informations légales et réglementaires
            </p>
        </div>
    </section>

    <!-- Contenu principal -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">

                <!-- Éditeur du site -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-building mr-2 text-purple-600"></i>Éditeur du site
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-tag text-purple-500 mt-1 mr-3 w-5"></i>
                                <div>
                                    <strong>Dénomination :</strong><br>
                                    <?= SITE_NAME ?>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-purple-500 mt-1 mr-3 w-5"></i>
                                <div>
                                    <strong>Siège social :</strong><br>
                                    Douala<br>
                                    Pariso Nyalla Kambo, Cameroun
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-phone text-purple-500 mt-1 mr-3 w-5"></i>
                                <div>
                                    <strong>Téléphone :</strong><br>
                                    +237 651 10 43 56
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-envelope text-purple-500 mt-1 mr-3 w-5"></i>
                                <div>
                                    <strong>Email :</strong><br>
                                    <?= SITE_EMAIL ?>
                                </div>
                            </div>
                            
                            <!--<div class="flex items-start">
                                <i class="fas fa-file-alt text-purple-500 mt-1 mr-3 w-5"></i>
                                <div>
                                    <strong>SIRET :</strong><br>
                                    123 456 789 00012
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-euro-sign text-purple-500 mt-1 mr-3 w-5"></i>
                                <div>
                                    <strong>N° TVA :</strong><br>
                                    FR12345678901
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>

                <!-- Directeur de publication -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-user-tie mr-2 text-blue-600"></i>Directeur de publication
                    </h2>
                    
                    <div class="bg-blue-50 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-blue-800">Djoukam Durand</h3>
                                <p class="text-blue-600">Directeur Général</p>
                            </div>
                        </div>
                        <p class="text-blue-700">
                            <i class="fas fa-envelope mr-2"></i>
                            Contact : direction@<?= strtolower(str_replace('https://', '', SITE_URL)) ?>
                        </p>
                    </div>
                </div>

                <!-- Hébergement -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-server mr-2 text-green-600"></i>Hébergement
                    </h2>
                    
                    <div class="bg-green-50 rounded-lg p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-semibold text-green-800 mb-3">Hébergeur principal</h3>
                                <div class="space-y-2 text-green-700">
                                    <p><strong>INFINITYFREE</strong></p>
                                    <p>2 rue Kellermann</p>
                                    <p>59100 Roubaix, France</p>
                                    <p><i class="fas fa-phone mr-2"></i>+33 9 72 10 10 07</p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-green-800 mb-3">CDN et sécurité</h3>
                                <div class="space-y-2 text-green-700">
                                    <p><strong>Cloudflare Inc.</strong></p>
                                    <p>101 Townsend St</p>
                                    <p>San Francisco, CA 94107, USA</p>
                                    <p><i class="fas fa-globe mr-2"></i>www.cloudflare.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Propriété intellectuelle -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-copyright mr-2 text-orange-600"></i>Propriété intellectuelle
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="bg-orange-50 rounded-lg p-4">
                            <h3 class="font-semibold text-orange-800 mb-2">Droits d'auteur</h3>
                            <p class="text-orange-700">
                                Le site <?= SITE_NAME ?>, sa structure générale, ses textes, images, sons et vidéos, 
                                ainsi que tous les éléments qui le composent, sont protégés par le droit d'auteur.
                            </p>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-semibold text-yellow-800 mb-2">Marques</h3>
                            <p class="text-yellow-700">
                                <?= SITE_NAME ?> est une marque déposée. Toute reproduction non autorisée 
                                est strictement interdite et constitue une contrefaçon.
                            </p>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4">
                            <h3 class="font-semibold text-red-800 mb-2">Contenus téléchargés</h3>
                            <p class="text-red-700">
                                Les utilisateurs sont seuls responsables du respect des droits d'auteur 
                                des contenus qu'ils téléchargent via notre service.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Données personnelles -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-shield-alt mr-2 text-indigo-600"></i>Protection des données
                    </h2>
                    
                    <div class="bg-indigo-50 rounded-lg p-6">
                        <div class="flex items-start mb-4">
                            <i class="fas fa-info-circle text-indigo-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <h3 class="font-semibold text-indigo-800 mb-2">Conformité RGPD</h3>
                                <p class="text-indigo-700 mb-4">
                                    <?= SITE_NAME ?> respecte le Règlement Général sur la Protection des Données (RGPD) 
                                    et la loi "Informatique et Libertés" du 6 janvier 1978 modifiée.
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-indigo-800 mb-2">Responsable de traitement</h4>
                                <p class="text-indigo-600 text-sm">Djoukam Durand - <?= SITE_EMAIL ?></p>
                            </div>
                            <div>
                                <h4 class="font-medium text-indigo-800 mb-2">DPO (Délégué à la protection des données)</h4>
                                <p class="text-indigo-600 text-sm">dpo@<?= strtolower(str_replace('https://', '', SITE_URL)) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cookies -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-cookie-bite mr-2 text-yellow-600"></i>Utilisation des cookies
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-semibold text-yellow-800 mb-2">Types de cookies utilisés</h3>
                            <ul class="text-yellow-700 space-y-1 text-sm">
                                <li>• Cookies de fonctionnement (session utilisateur, préférences)</li>
                                <li>• Cookies analytiques (Google Analytics - anonymisés)</li>
                                <li>• Cookies de sécurité (protection CSRF, limitation de débit)</li>
                            </ul>
                        </div>
                        
                        <p class="text-gray-700">
                            Vous pouvez configurer votre navigateur pour refuser les cookies ou être alerté 
                            lors de leur utilisation. Certaines fonctionnalités du site peuvent être limitées.
                        </p>
                    </div>
                </div>

                <!-- Droit applicable -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-gavel mr-2 text-purple-600"></i>Droit applicable et juridiction
                    </h2>
                    
                    <div class="bg-purple-50 rounded-lg p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-semibold text-purple-800 mb-2">Droit applicable</h3>
                                <p class="text-purple-700">
                                    Les présentes mentions légales et l'utilisation du site sont régies 
                                    par le droit français.
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-purple-800 mb-2">Juridiction compétente</h3>
                                <p class="text-purple-700">
                                    En cas de litige, les tribunaux de Paris seront seuls compétents.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Crédits -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-heart mr-2 text-red-600"></i>Crédits et remerciements
                    </h2>
                    
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <i class="fab fa-youtube text-red-500 text-2xl mb-2"></i>
                            <h3 class="font-semibold mb-1">YouTube API</h3>
                            <p class="text-sm text-gray-600">Google LLC</p>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <i class="fab fa-font-awesome text-blue-500 text-2xl mb-2"></i>
                            <h3 class="font-semibold mb-1">Font Awesome</h3>
                            <p class="text-sm text-gray-600">Fonticons Inc.</p>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <i class="fab fa-css3-alt text-blue-400 text-2xl mb-2"></i>
                            <h3 class="font-semibold mb-1">Tailwind CSS</h3>
                            <p class="text-sm text-gray-600">Tailwind Labs Inc.</p>
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="bg-gray-100 rounded-lg p-6 mt-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-envelope mr-2"></i>Nous contacter
                    </h2>
                    <p class="text-gray-700">
                        Pour toute question relative aux présentes mentions légales, vous pouvez nous contacter :
                    </p>
                    <div class="mt-4 space-y-2">
                        <p class="text-gray-700">
                            <i class="fas fa-envelope mr-2 text-purple-600"></i>
                            <a href="mailto:<?= SITE_EMAIL ?>" class="text-purple-600 hover:underline"><?= SITE_EMAIL ?></a>
                        </p>
                        <p class="text-gray-700">
                            <i class="fas fa-phone mr-2 text-purple-600"></i>
                            +237 651 10 43 56
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-download text-purple-400 text-2xl mr-3"></i>
                        <span class="text-xl font-bold"><?= SITE_NAME ?></span>
                    </div>
                    <p class="text-gray-400">Le service professionnel de téléchargement YouTube avec API pour développeurs.</p>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Liens utiles</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="docs.php" class="hover:text-white">Documentation</a></li>
                        <li><a href="register.php" class="hover:text-white">Créer un compte</a></li>
                        <li><a href="support.php" class="hover:text-white">Support</a></li>
                        <li><a href="status.php" class="hover:text-white">Status</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Légal</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="cgu.php" class="hover:text-white">CGU</a></li>
                        <li><a href="mentions.php" class="text-purple-400">Mentions légales</a></li>
                        <li><a href="privacy.php" class="hover:text-white">Politique de confidentialité</a></li>
                        <li><a href="rgpd.php" class="hover:text-white">RGPD</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-envelope mr-2"></i><?= SITE_EMAIL ?></li>
                        <li><i class="fas fa-globe mr-2"></i><?= SITE_URL ?></li>
                        <li class="flex space-x-3 pt-2">
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-discord"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Menu mobile
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>