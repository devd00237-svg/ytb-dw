<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Politique de Confidentialité</title>
    <meta name="description" content="Politique de confidentialité et protection des données personnelles de <?= SITE_NAME ?>.">
    
    <!-- CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS personnalisé -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                Politique de
                <span class="block text-purple-200">Confidentialité</span>
            </h1>
            <p class="text-xl mb-8 text-purple-100 max-w-3xl mx-auto">
                Votre vie privée est importante pour nous
            </p>
            <div class="text-purple-200">
                <i class="fas fa-calendar mr-2"></i>
                Dernière mise à jour : <?= date('d/m/Y') ?>
            </div>
        </div>
    </section>

    <!-- Contenu principal -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">

                <!-- Introduction -->
                <div class="bg-blue-50 rounded-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-blue-800 mb-4">
                        <i class="fas fa-shield-alt mr-2"></i>Notre engagement
                    </h2>
                    <p class="text-blue-700 text-lg leading-relaxed">
                        <?= SITE_NAME ?> s'engage à protéger et respecter votre vie privée. Cette politique 
                        explique comment nous collectons, utilisons et protégeons vos données personnelles 
                        conformément au RGPD et à la loi Informatique et Libertés.
                    </p>
                </div>

                <!-- Données collectées -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-database mr-2 text-green-600"></i>Données que nous collectons
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- Visiteurs -->
                        <div class="border-l-4 border-green-500 pl-6">
                            <h3 class="text-xl font-semibold text-green-700 mb-3">Visiteurs (utilisation libre)</h3>
                            <div class="bg-green-50 rounded-lg p-4">
                                <ul class="text-green-800 space-y-2">
                                    <li><i class="fas fa-check mr-2"></i>Adresse IP (anonymisée après 24h)</li>
                                    <li><i class="fas fa-check mr-2"></i>Type de navigateur et système d'exploitation</li>
                                    <li><i class="fas fa-check mr-2"></i>Pages visitées et temps de session</li>
                                    <li><i class="fas fa-check mr-2"></i>URLs YouTube soumises (supprimées après traitement)</li>
                                </ul>
                                <p class="text-green-700 text-sm mt-3 italic">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Aucune inscription requise sans API, données minimales collectées
                                </p>
                            </div>
                        </div>

                        <!-- Utilisateurs API -->
                        <div class="border-l-4 border-blue-500 pl-6">
                            <h3 class="text-xl font-semibold text-blue-700 mb-3">Utilisateurs API (comptes)</h3>
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-medium text-blue-800 mb-2">Données d'identification</h4>
                                        <ul class="text-blue-700 text-sm space-y-1">
                                            <li>• Adresse email</li>
                                            <li>• Mot de passe (chiffré)</li>
                                            <li>• Date d'inscription</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-blue-800 mb-2">Données d'utilisation</h4>
                                        <ul class="text-blue-700 text-sm space-y-1">
                                            <li>• Historique des requêtes API</li>
                                            <li>• Quotas et limitations</li>
                                            <li>• Statistiques d'usage</li>
                                            <li>• Logs d'activité (30 jours)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Utilisation des données -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-cogs mr-2 text-purple-600"></i>Comment nous utilisons vos données
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h3 class="font-semibold text-purple-800 mb-2">
                                    <i class="fas fa-play mr-2"></i>Fonctionnement du service
                                </h3>
                                <ul class="text-purple-700 text-sm space-y-1">
                                    <li>• Traitement des demandes de téléchargement</li>
                                    <li>• Gestion des quotas API</li>
                                    <li>• Support technique</li>
                                    <li>• Amélioration des performances</li>
                                </ul>
                            </div>
                            
                            <div class="bg-orange-50 rounded-lg p-4">
                                <h3 class="font-semibold text-orange-800 mb-2">
                                    <i class="fas fa-chart-bar mr-2"></i>Analyses et statistiques
                                </h3>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• Statistiques d'utilisation anonymisées</li>
                                    <li>• Optimisation des performances</li>
                                    <li>• Détection des tendances d'usage</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-red-50 rounded-lg p-4">
                                <h3 class="font-semibold text-red-800 mb-2">
                                    <i class="fas fa-shield-alt mr-2"></i>Sécurité
                                </h3>
                                <ul class="text-red-700 text-sm space-y-1">
                                    <li>• Prévention des abus</li>
                                    <li>• Protection contre les attaques</li>
                                    <li>• Respect des limitations de débit</li>
                                    <li>• Détection de fraude</li>
                                </ul>
                            </div>
                            
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h3 class="font-semibold text-blue-800 mb-2">
                                    <i class="fas fa-envelope mr-2"></i>Communication
                                </h3>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• Notifications importantes</li>
                                    <li>• Mises à jour du service</li>
                                    <li>• Support client</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Base légale -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-balance-scale mr-2 text-indigo-600"></i>Base légale du traitement
                    </h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-indigo-50">
                                    <th class="border border-gray-300 px-4 py-3 text-left">Finalité</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left">Base légale</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left">Durée</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">Service de téléchargement</td>
                                    <td class="border border-gray-300 px-4 py-2">Exécution du contrat</td>
                                    <td class="border border-gray-300 px-4 py-2">Durée d'utilisation</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">Gestion des comptes API</td>
                                    <td class="border border-gray-300 px-4 py-2">Exécution du contrat</td>
                                    <td class="border border-gray-300 px-4 py-2">3 ans après fermeture</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">Sécurité et prévention</td>
                                    <td class="border border-gray-300 px-4 py-2">Intérêt légitime</td>
                                    <td class="border border-gray-300 px-4 py-2">1 an maximum</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">Amélioration du service</td>
                                    <td class="border border-gray-300 px-4 py-2">Intérêt légitime</td>
                                    <td class="border border-gray-300 px-4 py-2">2 ans (anonymisé)</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">Marketing (optionnel)</td>
                                    <td class="border border-gray-300 px-4 py-2">Consentement</td>
                                    <td class="border border-gray-300 px-4 py-2">Jusqu'au retrait</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Partage des données -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-share-alt mr-2 text-yellow-600"></i>Partage des données
                    </h2>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-lock text-green-600 text-2xl mr-3"></i>
                            <div>
                                <h3 class="text-xl font-semibold text-green-800">Principe de non-partage</h3>
                                <p class="text-green-700">Nous ne vendons, louons ou partageons jamais vos données personnelles à des fins commerciales.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-semibold text-yellow-800 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Exceptions légales
                            </h3>
                            <p class="text-yellow-700 text-sm">
                                Vos données peuvent être communiquées uniquement sur demande judiciaire ou administrative légale.
                            </p>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-800 mb-2">
                                <i class="fas fa-server mr-2"></i>Prestataires techniques
                            </h3>
                            <div class="text-blue-700 text-sm space-y-2">
                                <p>Nos prestataires agissent uniquement sur instruction et sont liés par des accords de confidentialité :</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>INFINITYFREE (hébergement serveurs - France)</li>
                                    <li>Cloudflare (CDN et sécurité - chiffrement bout en bout)</li>
                                    <li>Google Analytics (statistiques anonymisées - opt-out possible)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vos droits -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-user-shield mr-2 text-green-600"></i>Vos droits (RGPD)
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="bg-green-50 rounded-lg p-4">
                                <h3 class="font-semibold text-green-800 mb-2">
                                    <i class="fas fa-eye mr-2"></i>Droit d'accès
                                </h3>
                                <p class="text-green-700 text-sm">
                                    Consultez toutes les données que nous détenons sur vous
                                </p>
                            </div>
                            
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h3 class="font-semibold text-blue-800 mb-2">
                                    <i class="fas fa-edit mr-2"></i>Droit de rectification
                                </h3>
                                <p class="text-blue-700 text-sm">
                                    Corrigez ou mettez à jour vos informations personnelles
                                </p>
                            </div>
                            
                            <div class="bg-red-50 rounded-lg p-4">
                                <h3 class="font-semibold text-red-800 mb-2">
                                    <i class="fas fa-trash mr-2"></i>Droit à l'effacement
                                </h3>
                                <p class="text-red-700 text-sm">
                                    Demandez la suppression de vos données (droit à l'oubli)
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h3 class="font-semibold text-purple-800 mb-2">
                                    <i class="fas fa-download mr-2"></i>Droit à la portabilité
                                </h3>
                                <p class="text-purple-700 text-sm">
                                    Récupérez vos données dans un format exploitable
                                </p>
                            </div>
                            
                            <div class="bg-orange-50 rounded-lg p-4">
                                <h3 class="font-semibold text-orange-800 mb-2">
                                    <i class="fas fa-pause mr-2"></i>Droit de limitation
                                </h3>
                                <p class="text-orange-700 text-sm">
                                    Limitez temporairement le traitement de vos données
                                </p>
                            </div>
                            
                            <div class="bg-gray-100 rounded-lg p-4">
                                <h3 class="font-semibold text-gray-800 mb-2">
                                    <i class="fas fa-ban mr-2"></i>Droit d'opposition
                                </h3>
                                <p class="text-gray-700 text-sm">
                                    Opposez-vous au traitement pour motif légitime
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-50 rounded-lg p-6 mt-6">
                        <h3 class="font-semibold text-indigo-800 mb-3">
                            <i class="fas fa-paper-plane mr-2"></i>Comment exercer vos droits
                        </h3>
                        <div class="text-indigo-700 space-y-2">
                            <p><strong>Email :</strong> privacy@<?= strtolower(str_replace('https://', '', SITE_URL)) ?></p>
                            <p><strong>Délai de réponse :</strong> 1 mois maximum</p>
                            <p><strong>Pièce d'identité :</strong> Requise pour vérification</p>
                            <p><strong>Gratuit :</strong> Première demande sans frais</p>
                        </div>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-shield-alt mr-2 text-red-600"></i>Sécurité et protection
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-red-700 mb-3">Mesures techniques</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Chiffrement SSL/TLS (HTTPS)</span>
                                </div>
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Mots de passe chiffrés (bcrypt)</span>
                                </div>
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Pare-feu et protection DDoS</span>
                                </div>
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Sauvegarde automatique sécurisée</span>
                                </div>
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Audit de sécurité régulier</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-blue-700 mb-3">Mesures organisationnelles</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-blue-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Accès limité aux données (need-to-know)</span>
                                </div>
                                <div class="flex items-center text-blue-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Formation sécurité des équipes</span>
                                </div>
                                <div class="flex items-center text-blue-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Procédures d'incident documentées</span>
                                </div>
                                <div class="flex items-center text-blue-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Surveillance continue des systèmes</span>
                                </div>
                                <div class="flex items-center text-blue-600">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-gray-700">Contrats de confidentialité signés</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 rounded-lg p-4 mt-6">
                        <h3 class="font-semibold text-red-800 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>En cas de violation de données
                        </h3>
                        <p class="text-red-700 text-sm">
                            Nous nous engageons à vous notifier dans les 72h en cas de violation de sécurité 
                            concernant vos données personnelles, conformément au RGPD.
                        </p>
                    </div>
                </div>

                <!-- Cookies -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-cookie-bite mr-2 text-yellow-600"></i>Gestion des cookies
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-yellow-50">
                                        <th class="border border-gray-300 px-4 py-3 text-left">Type</th>
                                        <th class="border border-gray-300 px-4 py-3 text-left">Finalité</th>
                                        <th class="border border-gray-300 px-4 py-3 text-left">Durée</th>
                                        <th class="border border-gray-300 px-4 py-3 text-left">Consentement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2 font-medium">Fonctionnels</td>
                                        <td class="border border-gray-300 px-4 py-2">Session utilisateur, préférences</td>
                                        <td class="border border-gray-300 px-4 py-2">Session / 1 an</td>
                                        <td class="border border-gray-300 px-4 py-2 text-green-600">
                                            <i class="fas fa-check"></i> Obligatoires
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2 font-medium">Sécurité</td>
                                        <td class="border border-gray-300 px-4 py-2">Protection CSRF, anti-spam</td>
                                        <td class="border border-gray-300 px-4 py-2">24h</td>
                                        <td class="border border-gray-300 px-4 py-2 text-green-600">
                                            <i class="fas fa-check"></i> Obligatoires
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2 font-medium">Analytiques</td>
                                        <td class="border border-gray-300 px-4 py-2">Google Analytics (anonymisé)</td>
                                        <td class="border border-gray-300 px-4 py-2">2 ans</td>
                                        <td class="border border-gray-300 px-4 py-2 text-orange-600">
                                            <i class="fas fa-hand-paper"></i> Optionnel
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-semibold text-yellow-800 mb-2">
                                <i class="fas fa-cog mr-2"></i>Gestion des préférences
                            </h3>
                            <p class="text-yellow-700 text-sm mb-3">
                                Vous pouvez modifier vos préférences de cookies à tout moment :
                            </p>
                            <div class="space-y-2 text-yellow-700 text-sm">
                                <div>• <strong>Navigateur :</strong> Paramètres > Confidentialité > Cookies</div>
                                <div>• <strong>Google Analytics :</strong> <a href="#" class="underline">Opt-out disponible</a></div>
                                <div>• <strong>Bannière :</strong> Cliquez sur "Paramètres des cookies" en bas de page</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transferts internationaux -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-globe mr-2 text-indigo-600"></i>Transferts de données
                    </h2>
                    
                    <div class="bg-indigo-50 rounded-lg p-6">
                        <div class="flex items-start mb-4">
                            <i class="fas fa-flag text-indigo-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <h3 class="font-semibold text-indigo-800 mb-2">Hébergement européen</h3>
                                <p class="text-indigo-700">
                                    Vos données sont stockées et traitées exclusivement en France (INFINITYFREE) et dans l'Union Européenne, 
                                    garantissant le respect du RGPD.
                                </p>
                            </div>
                        </div>
                        
                        <div class="bg-blue-100 rounded-lg p-4 mt-4">
                            <h4 class="font-medium text-blue-800 mb-2">Services tiers</h4>
                            <ul class="text-blue-700 text-sm space-y-1">
                                <li>• <strong>Cloudflare :</strong> Clauses contractuelles types UE approuvées</li>
                                <li>• <strong>Google Analytics :</strong> Anonymisation IP + accord de traitement des données</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mineurs -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-child mr-2 text-pink-600"></i>Protection des mineurs
                    </h2>
                    
                    <div class="bg-pink-50 rounded-lg p-6">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-pink-600 text-2xl mr-3 mt-1"></i>
                            <div>
                                <h3 class="font-semibold text-pink-800 mb-2">Politique stricte</h3>
                                <div class="text-pink-700 space-y-2">
                                    <p>
                                        Notre service ne collecte pas intentionnellement de données personnelles 
                                        d'enfants de moins de 16 ans (13 ans selon la juridiction).
                                    </p>
                                    <p>
                                        <strong>Si nous découvrons :</strong> suppression immédiate des données 
                                        et contact des parents/tuteurs si possible.
                                    </p>
                                    <p>
                                        <strong>Parents/tuteurs :</strong> Contactez-nous à 
                                        <a href="mailto:privacy@<?= strtolower(str_replace('https://', '', SITE_URL)) ?>" 
                                           class="underline">privacy@<?= strtolower(str_replace('https://', '', SITE_URL)) ?></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact DPO -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-user-shield mr-2 text-purple-600"></i>Délégué à la Protection des Données
                    </h2>
                    
                    <div class="bg-purple-50 rounded-lg p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <div class="flex items-center mb-4">
                                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-user-tie text-purple-600 text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold text-purple-800">Ralph Urgue</h3>
                                        <p class="text-purple-600">DPO Certifiée</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 text-purple-700">
                                    <p><i class="fas fa-envelope mr-2"></i>dpo@<?= strtolower(str_replace('https://', '', SITE_URL)) ?>.</p>
                                    <p><i class="fas fa-phone mr-2"></i>+237 x xx xx xx xx</p>
                                    <p><i class="fas fa-clock mr-2"></i>Lundi - Vendredi : 9h - 17h</p>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-semibold text-purple-800 mb-3">Missions du DPO</h4>
                                <ul class="text-purple-700 text-sm space-y-1">
                                    <li>• Conseil et accompagnement RGPD</li>
                                    <li>• Traitement des demandes d'exercice des droits</li>
                                    <li>• Point de contact avec la CNIL</li>
                                    <li>• Formation et sensibilisation des équipes</li>
                                    <li>• Réalisation d'audits de conformité</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Réclamation -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>Réclamation et autorité de contrôle
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="bg-orange-50 rounded-lg p-4">
                            <h3 class="font-semibold text-orange-800 mb-2">Procédure interne</h3>
                            <p class="text-orange-700 text-sm">
                                En cas de problème, contactez d'abord notre DPO. Nous nous engageons à traiter 
                                votre demande dans les meilleurs délais.
                            </p>
                        </div>
                        
                        <!--<div class="bg-red-50 rounded-lg p-6">
                            <h3 class="font-semibold text-red-800 mb-3">
                                <i class="fas fa-landmark mr-2"></i>CNIL - Commission Nationale de l'Informatique et des Libertés
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2 text-red-700 text-sm">
                                    <p><strong>Adresse :</strong><br>3 Place de Fontenoy<br>TSA 80715<br>75334 Paris Cedex 07</p>
                                    <p><strong>Téléphone :</strong> 01 53 73 22 22</p>
                                </div>
                                <div class="space-y-2 text-red-700 text-sm">
                                    <p><strong>Site web :</strong> www.cnil.fr</p>
                                    <p><strong>Plainte en ligne :</strong> Formulaire disponible sur cnil.fr</p>
                                    <p><strong>Droit :</strong> Réclamation gratuite</p>
                                </div>
                            </div>
                        </div>-->
                    </div>
                </div>

                <!-- Contact -->
                <div class="bg-gray-100 rounded-lg p-6 mt-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-envelope mr-2"></i>Questions sur cette politique
                    </h2>
                    <p class="text-gray-700 mb-4">
                        Pour toute question concernant cette politique de confidentialité ou l'exercice de vos droits :
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <p class="text-gray-700">
                                <i class="fas fa-envelope mr-2 text-purple-600"></i>
                                <a href="mailto:privacy@<?= strtolower(str_replace('https://', '', SITE_URL)) ?>." class="text-purple-600 hover:underline">
                                    privacy@<?= strtolower(str_replace('https://', '', SITE_URL)) ?>.
                                </a>
                            </p>
                            <p class="text-gray-700">
                                <i class="fas fa-user-shield mr-2 text-purple-600"></i>
                                <a href="mailto:dpo@<?= strtolower(str_replace('https://', '', SITE_URL)) ?>." class="text-purple-600 hover:underline">
                                    dpo@<?= strtolower(str_replace('https://', '', SITE_URL)) ?>.
                                </a>
                            </p>
                        </div>
                        <div class="text-gray-600 text-sm">
                            <p><strong>Délai de réponse :</strong> 1 mois maximum</p>
                            <p><strong>Service gratuit</strong> (première demande)</p>
                        </div>
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
                        <li><a href="mentions.php" class="hover:text-white">Mentions légales</a></li>
                        <li><a href="privacy.php" class="text-purple-400">Politique de confidentialité</a></li>
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