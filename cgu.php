<?php
require_once 'config.php';

// Récupérer les statistiques du site
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM site_stats LIMIT 1");
$stats = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Conditions Générales d'Utilisation</title>
    <meta name="description" content="Conditions générales d'utilisation de <?= SITE_NAME ?>, service professionnel de téléchargement YouTube.">
    
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
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
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
        
        /* Ajustement responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 64px; /* Hauteur réduite sur mobile */
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
                Conditions Générales
                <span class="block text-purple-200">d'Utilisation</span>
            </h1>
            <p class="text-xl mb-8 text-purple-100 max-w-3xl mx-auto">
                Dernière mise à jour : <?= date('d/m/Y') ?>
            </p>
        </div>
    </section>

    <!-- Contenu principal -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                
                <!-- Introduction -->
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-blue-800 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Préambule
                    </h2>
                    <p class="text-blue-700">
                        Les présentes conditions générales d'utilisation régissent l'utilisation de <?= SITE_NAME ?>, 
                        service de téléchargement de contenus YouTube. En utilisant notre service, vous acceptez 
                        ces conditions dans leur intégralité.
                    </p>
                </div>

                <!-- Section 1 -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">1. Objet du service</h2>
                    
                    <div class="prose prose-lg max-w-none">
                        <p class="mb-4">
                            <?= SITE_NAME ?> propose un service de téléchargement de contenus vidéo et audio depuis YouTube. 
                            Le service est accessible :
                        </p>
                        <ul class="list-disc pl-6 mb-4 space-y-2">
                            <li>Gratuitement pour les visiteurs sans limitation de téléchargements</li>
                            <li>Via API pour les développeurs avec quotas selon le type de compte</li>
                            <li>24h/24 et 7j/7 depuis notre interface web</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">2. Utilisation autorisée</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-semibold text-green-800 mb-3">
                                <i class="fas fa-check mr-2"></i>Usages autorisés
                            </h3>
                            <ul class="text-green-700 space-y-2">
                                <li>• Téléchargement pour usage personnel</li>
                                <li>• Sauvegarde de vos propres contenus</li>
                                <li>• Usage éducatif non commercial</li>
                                <li>• Intégration API dans vos applications</li>
                            </ul>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4">
                            <h3 class="font-semibold text-red-800 mb-3">
                                <i class="fas fa-times mr-2"></i>Usages interdits
                            </h3>
                            <ul class="text-red-700 space-y-2">
                                <li>• Distribution commerciale</li>
                                <li>• Violation du droit d'auteur</li>
                                <li>• Revente des contenus téléchargés</li>
                                <li>• Utilisation abusive de l'API</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">3. Responsabilités</h2>
                    
                    <div class="space-y-4">
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h3 class="font-semibold mb-2">Responsabilité de l'utilisateur</h3>
                            <p class="text-gray-700">
                                L'utilisateur est seul responsable de l'usage qu'il fait des contenus téléchargés 
                                et doit respecter les droits d'auteur et la législation applicable.
                            </p>
                        </div>
                        
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h3 class="font-semibold mb-2">Responsabilité de <?= SITE_NAME ?></h3>
                            <p class="text-gray-700">
                                <?= SITE_NAME ?> met à disposition un outil technique et ne peut être tenu responsable 
                                de l'usage fait par les utilisateurs des contenus téléchargés.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 4 -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">4. API et quotas</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2 text-left">Type de compte</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Quota quotidien</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Limitations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">Visiteur</td>
                                    <td class="border border-gray-300 px-4 py-2">Illimité</td>
                                    <td class="border border-gray-300 px-4 py-2">Interface web uniquement</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">API Gratuite</td>
                                    <td class="border border-gray-300 px-4 py-2">10 requêtes</td>
                                    <td class="border border-gray-300 px-4 py-2">Formats audio uniquement</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">API Premium</td>
                                    <td class="border border-gray-300 px-4 py-2">100 requêtes</td>
                                    <td class="border border-gray-300 px-4 py-2">Tous formats </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section 5 -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">5. Disponibilité du service</h2>
                    
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Nous nous efforçons de maintenir une disponibilité de 99.9% mais ne garantissons pas 
                            un service ininterrompu. Des maintenances programmées peuvent occasionner des interruptions temporaires.
                        </p>
                    </div>
                </div>

                <!-- Section 6 -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">6. Modifications des CGU</h2>
                    
                    <p class="text-gray-700 mb-4">
                        <?= SITE_NAME ?> se réserve le droit de modifier ces conditions à tout moment. 
                        Les utilisateurs seront informés des modifications majeures par email ou notification sur le site.
                    </p>
                    
                    <div class="bg-blue-100 rounded-lg p-4">
                        <p class="text-blue-800">
                            <i class="fas fa-bell mr-2"></i>
                            La poursuite de l'utilisation du service après modification vaut acceptation des nouvelles conditions.
                        </p>
                    </div>
                </div>

                <!-- Contact -->
                <div class="bg-gray-100 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Contact</h2>
                    <p class="text-gray-700">
                        Pour toute question concernant ces conditions d'utilisation, contactez-nous à :
                        <a href="mailto:<?= SITE_EMAIL ?>" class="text-purple-600 hover:underline"><?= SITE_EMAIL ?></a>
                    </p>
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
                        <li><a href="cgu.php" class="text-purple-400">CGU</a></li>
                        <li><a href="mentions.php" class="hover:text-white">Mentions légales</a></li>
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