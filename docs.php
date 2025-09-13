<?php require_once 'config.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation API - <?= SITE_NAME ?></title>
    <meta name="description" content="Documentation complète de l'API <?= SITE_NAME ?> pour développeurs. Guides, exemples et références.">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar-sticky {
            position: sticky;
            top: 6rem; /* Ajusté pour tenir compte du header fixe */
            max-height: calc(100vh - 8rem);
            overflow-y: auto;
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
        
        /* Code blocks responsive */
        pre {
            border-radius: 0.5rem !important;
            font-size: 0.875rem;
            max-width: 100%;
            overflow-x: auto;
        }
        
        pre code {
            white-space: pre;
            word-wrap: normal;
            overflow-wrap: normal;
        }
        
        .copy-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 10;
        }
        
        pre:hover .copy-btn {
            opacity: 1;
        }
        
        /* Tables responsives */
        .table-container {
            overflow-x: auto;
            max-width: 100%;
        }
        
        .table-container table {
            min-width: 600px;
        }
        
        /* Conteneur principal responsive */
        .main-container {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Transition douce quand la sidebar devient fixed */
.sidebar-fixed {
  position: fixed !important;
  z-index: 1;
  transition: top 0.3s ease, transform 0.3s ease, opacity 0.3s ease;
  transform: translateY(-10px);
  opacity: 0.95;
  border-top-left-radius: 0px;
  border-top-right-radius: 0px;
}
.sidebar-fixed.active {
  transform: translateY(0);
  opacity: 1;
}
.sidebar-placeholder {
  visibility: hidden;
}
        
        /* Sidebar responsive */
        @media (max-width: 1024px) {
            .sidebar-sticky {
                position: relative;
                top: auto;
                max-height: none;
                margin-bottom: 2rem;
            }
            
            body {
                padding-top: 64px; /* Hauteur réduite sur mobile */
            }
        }
        
        /* Code responsif pour mobile */
        @media (max-width: 640px) {
            pre {
                font-size: 0.75rem;
                padding: 0.75rem;
            }
            
            .copy-btn {
                position: static;
                opacity: 1;
                margin-top: 0.5rem;
                display: block;
                width: 100%;
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
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-purple-600 transition-colors">Accueil</a>
                    <a href="docs.php" class="text-purple-600 font-medium">Documentation</a>
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
            <div class="px-4 sm:px-6 py-3 space-y-2">
                <a href="index.php" class="block text-gray-700 py-2">Accueil</a>
                <a href="docs.php" class="block text-purple-600 py-2 font-medium">Documentation</a>
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

    <!-- Header -->
    <div class="gradient-bg text-white py-12">
        <div class="container mx-auto px-4 sm:px-6">
            <h1 class="text-3xl sm:text-4xl font-bold mb-4">Documentation API</h1>
            <p class="text-lg sm:text-xl text-purple-100">Guide complet pour intégrer <?= SITE_NAME ?> dans vos applications</p>
        </div>
    </div>

    <div class="main-container container mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="sidebar-sticky bg-white rounded-lg p-6 shadow-lg">
                    <nav class="space-y-2">
                        <a href="#getting-started" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-rocket mr-2"></i>Démarrage rapide
                        </a>
                        <a href="#authentication" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-key mr-2"></i>Authentification
                        </a>
                        <a href="#account-types" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-users mr-2"></i>Types de comptes
                        </a>
                        <a href="#endpoints" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-code mr-2"></i>Endpoints
                        </a>
                        <a href="#examples" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-file-code mr-2"></i>Exemples
                        </a>
                        <a href="#errors" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Codes d'erreur
                        </a>
                        <a href="#rate-limits" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>Limites
                        </a>
                        <a href="#sdks" class="block py-2 px-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors">
                            <i class="fas fa-cube mr-2"></i>SDKs
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="flex-1 min-w-0 space-y-8">
                <!-- Démarrage rapide -->
                <section id="getting-started" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-rocket mr-3 text-purple-600"></i>Démarrage rapide
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">1. Créer un compte</h3>
                            <p class="text-gray-600 mb-4">
                                Inscrivez-vous sur <?= SITE_NAME ?> pour obtenir automatiquement votre première clé API.
                            </p>
                            <a href="register.php" class="inline-flex items-center bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-user-plus mr-2"></i>
                                Créer un compte gratuit
                            </a>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-3">2. Premier test</h3>
                            <p class="text-gray-600 mb-4">Testez votre clé API avec cette requête simple :</p>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code class="text-sm">curl -X GET "<?= SITE_URL ?>/download.php?api_key=VOTRE_CLE&url=https://youtube.com/watch?v=dQw4w9WgXcQ&format=audio"</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Authentification -->
                <section id="authentication" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-key mr-3 text-purple-600"></i>Authentification
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Clé API</h3>
                            <p class="text-gray-600 mb-4">
                                Toutes les requêtes API doivent inclure votre clé API dans le paramètre <code class="bg-gray-100 px-2 py-1 rounded">api_key</code>.
                            </p>
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <i class="fas fa-info-circle text-blue-400 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-blue-700 font-medium">Format de la clé API</p>
                                        <p class="text-blue-600 text-sm">Les clés API commencent toujours par <code>ytb-dw-</code> suivi de 32 caractères alphanumériques.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Sécurité</h3>
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle text-yellow-400 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-yellow-700 font-medium">Gardez votre clé secrète</p>
                                        <p class="text-yellow-600 text-sm">Ne partagez jamais votre clé API publiquement. Utilisez des variables d'environnement dans vos applications.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Types de comptes -->
                <section id="account-types" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-users mr-3 text-purple-600"></i>Types de comptes
                    </h2>
                    
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Gratuit -->
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-leaf text-green-600 text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold">Gratuit</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600 mb-4">
                                <li><i class="fas fa-check text-green-500 mr-2"></i><?= QUOTA_GRATUIT ?> téléchargements/jour</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Audio MP3 uniquement</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Support communautaire</li>
                            </ul>
                            <div class="text-2xl font-bold text-green-600">Gratuit</div>
                        </div>

                        <!-- Premium -->
                        <div class="border-2 border-purple-500 rounded-lg p-6 relative">
                            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                                <span class="bg-purple-500 text-white px-3 py-1 rounded-full text-sm font-medium">Populaire</span>
                            </div>
                            <div class="flex items-center mb-4">
                                <i class="fas fa-crown text-purple-600 text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold">Premium</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600 mb-4">
                                <li><i class="fas fa-check text-green-500 mr-2"></i><?= QUOTA_PREMIUM ?> téléchargements/jour</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Tous formats (MP3, MP4)</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Support prioritaire</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>API avancée</li>
                            </ul>
                            <div class="text-2xl font-bold text-purple-600">9,99€/mois</div>
                        </div>

                        <!-- Unlimited -->
                        <div class="border border-gray-200 rounded-lg p-6 md:col-span-2 lg:col-span-1">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-infinity text-yellow-600 text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold">Unlimited</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600 mb-4">
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Téléchargements illimités</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Tous formats</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Support 24/7</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>SLA 99.9%</li>
                            </ul>
                            <div class="text-2xl font-bold text-yellow-600">Sur mesure</div>
                        </div>
                    </div>
                </section>

                <!-- Endpoints -->
                <section id="endpoints" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-code mr-3 text-purple-600"></i>Endpoints API
                    </h2>
                    
                    <div class="space-y-8">
                        <!-- Téléchargement -->
                        <div>
                            <div class="flex flex-wrap items-center mb-4 gap-2">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">GET</span>
                                <h3 class="text-lg font-semibold">/download.php</h3>
                            </div>
                            <p class="text-gray-600 mb-4">Télécharge une vidéo YouTube avec votre clé API.</p>
                            
                            <h4 class="font-medium mb-2">Paramètres</h4>
                            <div class="table-container">
                                <table class="w-full bg-gray-50 rounded-lg">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="px-4 py-2 text-left text-sm font-medium">Paramètre</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium">Type</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium">Obligatoire</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        <tr class="border-t">
                                            <td class="px-4 py-2 font-mono text-xs">api_key</td>
                                            <td class="px-4 py-2">string</td>
                                            <td class="px-4 py-2 text-red-600">Oui</td>
                                            <td class="px-4 py-2">Votre clé API</td>
                                        </tr>
                                        <tr class="border-t">
                                            <td class="px-4 py-2 font-mono text-xs">url</td>
                                            <td class="px-4 py-2">string</td>
                                            <td class="px-4 py-2 text-red-600">Oui</td>
                                            <td class="px-4 py-2">URL YouTube complète</td>
                                        </tr>
                                        <tr class="border-t">
                                            <td class="px-4 py-2 font-mono text-xs">format</td>
                                            <td class="px-4 py-2">string</td>
                                            <td class="px-4 py-2 text-blue-600">Non</td>
                                            <td class="px-4 py-2">audio ou video (défaut: video)</td>
                                        </tr>
                                        <tr class="border-t">
                                            <td class="px-4 py-2 font-mono text-xs">quality</td>
                                            <td class="px-4 py-2">string</td>
                                            <td class="px-4 py-2 text-blue-600">Non</td>
                                            <td class="px-4 py-2">480, 720, 1080, best (défaut: best)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h4 class="font-medium mb-2 mt-4">Exemple de requête</h4>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm break-all">GET <?= SITE_URL ?>/download.php?api_key=ytb-dw-abc123&url=https://youtube.com/watch?v=dQw4w9WgXcQ&format=video&quality=720</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                        </div>

                        <!-- Informations vidéo -->
                        <div>
                            <div class="flex flex-wrap items-center mb-4 gap-2">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">GET</span>
                                <h3 class="text-lg font-semibold">/video_info.php</h3>
                            </div>
                            <p class="text-gray-600 mb-4">Récupère les métadonnées d'une vidéo sans la télécharger.</p>
                            
                            <h4 class="font-medium mb-2">Paramètres</h4>
                            <div class="table-container">
                                <table class="w-full bg-gray-50 rounded-lg">
                                    <tbody class="text-sm">
                                        <tr class="border-t">
                                            <td class="px-4 py-2 font-mono text-xs">url</td>
                                            <td class="px-4 py-2">string</td>
                                            <td class="px-4 py-2 text-red-600">Oui</td>
                                            <td class="px-4 py-2">URL YouTube complète</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h4 class="font-medium mb-2 mt-4">Exemple de réponse</h4>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm">{
  "success": true,
  "title": "Never Gonna Give You Up",
  "duration": "3:33",
  "uploader": "RickAstleyVEVO",
  "view_count": 1234567890,
  "formats": [
    {
      "type": "Audio MP3",
      "quality": "192 kbps",
      "size": "8.2 Mo"
    },
    {
      "type": "Vidéo MP4 720p",
      "quality": "720p - 30 fps",
      "size": "45.1 Mo"
    }
  ]
}</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Exemples -->
                <section id="examples" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-file-code mr-3 text-purple-600"></i>Exemples de code
                    </h2>
                    
                    <div class="space-y-8">
                        <!-- JavaScript -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">JavaScript / Node.js</h3>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm">const apiKey = 'ytb-dw-votre-cle-api';
const youtubeUrl = 'https://youtube.com/watch?v=dQw4w9WgXcQ';

// Récupérer les infos de la vidéo
fetch(`<?= SITE_URL ?>/video_info.php?url=${encodeURIComponent(youtubeUrl)}`)
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Titre:', data.title);
      console.log('Durée:', data.duration);
      console.log('Formats disponibles:', data.formats);
      
      // Télécharger en MP3
      const downloadUrl = `<?= SITE_URL ?>/download.php?api_key=${apiKey}&url=${encodeURIComponent(youtubeUrl)}&format=audio`;
      window.open(downloadUrl, '_blank');
    }
  })
  .catch(error => console.error('Erreur:', error));</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                        </div>

                        <!-- Python -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Python</h3>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm">import requests
import json

API_KEY = 'ytb-dw-votre-cle-api'
YOUTUBE_URL = 'https://youtube.com/watch?v=dQw4w9WgXcQ'

# Récupérer les informations de la vidéo
info_url = '<?= SITE_URL ?>/video_info.php'
info_response = requests.get(info_url, params={'url': YOUTUBE_URL})
video_info = info_response.json()

if video_info.get('success'):
    print(f"Titre: {video_info['title']}")
    print(f"Durée: {video_info['duration']}")
    
    # Télécharger la vidéo en 720p
    download_url = '<?= SITE_URL ?>/download.php'
    download_params = {
        'api_key': API_KEY,
        'url': YOUTUBE_URL,
        'format': 'video',
        'quality': '720'
    }
    
    response = requests.get(download_url, params=download_params, stream=True)
    
    if response.status_code == 200:
        with open('video.mp4', 'wb') as f:
            for chunk in response.iter_content(chunk_size=8192):
                f.write(chunk)
        print("Téléchargement terminé!")
    else:
        print(f"Erreur: {response.status_code}")
else:
    print(f"Erreur: {video_info.get('error')}")
</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                        </div>

                        <!-- PHP -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">PHP</h3>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm"><?php
$apiKey = 'ytb-dw-votre-cle-api';
$youtubeUrl = 'https://youtube.com/watch?v=dQw4w9WgXcQ';

// Récupérer les informations de la vidéo
$infoUrl = '<?= SITE_URL ?>/video_info.php?' . http_build_query(['url' => $youtubeUrl]);
$videoInfo = json_decode(file_get_contents($infoUrl), true);

if ($videoInfo['success']) {
    echo "Titre: " . $videoInfo['title'] . "\n";
    echo "Durée: " . $videoInfo['duration'] . "\n";
    
    // Télécharger en audio MP3
    $downloadUrl = '<?= SITE_URL ?>/download.php?' . http_build_query([
        'api_key' => $apiKey,
        'url' => $youtubeUrl,
        'format' => 'audio'
    ]);
    
    $audioData = file_get_contents($downloadUrl);
    file_put_contents('audio.mp3', $audioData);
    echo "Téléchargement terminé!\n";
} else {
    echo "Erreur: " . $videoInfo['error'] . "\n";
}
?>
</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                        </div>

                        <!-- cURL -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">cURL (Bash)</h3>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm">#!/bin/bash

API_KEY="ytb-dw-votre-cle-api"
YOUTUBE_URL="https://youtube.com/watch?v=dQw4w9WgXcQ"

# Récupérer les informations de la vidéo
echo "Récupération des informations..."
curl -s "<?= SITE_URL ?>/video_info.php?url=${YOUTUBE_URL}" | jq '.'

# Télécharger la vidéo en 1080p
echo "Téléchargement en cours..."
curl -L -o "video.mp4" "<?= SITE_URL ?>/download.php?api_key=${API_KEY}&url=${YOUTUBE_URL}&format=video&quality=1080"

echo "Téléchargement terminé!"
</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Codes d'erreur -->
                <section id="errors" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-exclamation-triangle mr-3 text-purple-600"></i>Codes d'erreur
                    </h2>
                    
                    <div class="table-container">
                        <table class="w-full bg-gray-50 rounded-lg">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left text-sm font-medium">Code HTTP</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium">Erreur</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium">Description</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium">Solution</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-mono">400</td>
                                    <td class="px-4 py-2">Bad Request</td>
                                    <td class="px-4 py-2">Paramètres manquants ou invalides</td>
                                    <td class="px-4 py-2">Vérifiez vos paramètres</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-mono">401</td>
                                    <td class="px-4 py-2">Unauthorized</td>
                                    <td class="px-4 py-2">Clé API invalide ou inactive</td>
                                    <td class="px-4 py-2">Vérifiez votre clé API</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-mono">403</td>
                                    <td class="px-4 py-2">Forbidden</td>
                                    <td class="px-4 py-2">Format non autorisé pour votre compte</td>
                                    <td class="px-4 py-2">Upgradez votre compte</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-mono">404</td>
                                    <td class="px-4 py-2">Not Found</td>
                                    <td class="px-4 py-2">Vidéo introuvable ou privée</td>
                                    <td class="px-4 py-2">Vérifiez l'URL de la vidéo</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-mono">429</td>
                                    <td class="px-4 py-2">Too Many Requests</td>
                                    <td class="px-4 py-2">Quota quotidien dépassé</td>
                                    <td class="px-4 py-2">Attendez minuit ou upgradez</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-mono">500</td>
                                    <td class="px-4 py-2">Internal Server Error</td>
                                    <td class="px-4 py-2">Erreur serveur temporaire</td>
                                    <td class="px-4 py-2">Réessayez dans quelques minutes</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Limites -->
                <section id="rate-limits" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-tachometer-alt mr-3 text-purple-600"></i>Limites et quotas
                    </h2>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-3">Quotas quotidiens</h3>
                            <ul class="space-y-2 text-yellow-700">
                                <li><strong>Gratuit:</strong> <?= QUOTA_GRATUIT ?> téléchargements/jour</li>
                                <li><strong>Premium:</strong> <?= QUOTA_PREMIUM ?> téléchargements/jour</li>
                                <li><strong>Unlimited:</strong> Illimité</li>
                            </ul>
                            <p class="text-sm text-yellow-600 mt-3">
                                Les quotas se remettent à zéro chaque jour à minuit (UTC+1).
                            </p>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">Limitations techniques</h3>
                            <ul class="space-y-2 text-blue-700">
                                <li><strong>Timeout:</strong> 15 minutes max par téléchargement</li>
                                <li><strong>Taille max:</strong> 2 Go par fichier</li>
                                <li><strong>Formats:</strong> Selon le type de compte</li>
                                <li><strong>Concurrent:</strong> 3 téléchargements simultanés</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- SDKs -->
                <section id="sdks" class="bg-white rounded-lg p-6 sm:p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-cube mr-3 text-purple-600"></i>SDKs et outils
                    </h2>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-3">
                                <i class="fab fa-js-square text-yellow-500 mr-2"></i>SDK JavaScript
                            </h3>
                            <p class="text-gray-600 mb-4">SDK officiel pour Node.js et navigateurs</p>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto"><code>npm install @ytdownloader/sdk</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                            <a href="#" class="inline-block mt-3 text-purple-600 hover:text-purple-800 text-sm">
                                Documentation du SDK →
                            </a>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-3">
                                <i class="fab fa-python text-blue-500 mr-2"></i>SDK Python
                            </h3>
                            <p class="text-gray-600 mb-4">Package pip pour Python 3.6+</p>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto"><code>pip install ytdownloader-sdk</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                            <a href="#" class="inline-block mt-3 text-purple-600 hover:text-purple-800 text-sm">
                                Documentation du SDK →
                            </a>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-3">
                                <i class="fab fa-php text-purple-500 mr-2"></i>SDK PHP
                            </h3>
                            <p class="text-gray-600 mb-4">Composer package pour PHP 7.4+</p>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto"><code>composer require ytdownloader/sdk</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                            <a href="#" class="inline-block mt-3 text-purple-600 hover:text-purple-800 text-sm">
                                Documentation du SDK →
                            </a>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-3">
                                <i class="fas fa-terminal text-gray-600 mr-2"></i>CLI Tool
                            </h3>
                            <p class="text-gray-600 mb-4">Outil en ligne de commande</p>
                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto"><code>npm install -g ytdownloader-cli</code></pre>
                                <button class="copy-btn bg-gray-700 text-white px-2 py-1 rounded text-xs hover:bg-gray-600">
                                    Copier
                                </button>
                            </div>
                            <a href="#" class="inline-block mt-3 text-purple-600 hover:text-purple-800 text-sm">
                                Documentation CLI →
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Support -->
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg p-6 sm:p-8 text-white text-center">
                    <h2 class="text-2xl font-bold mb-4">Besoin d'aide ?</h2>
                    <p class="text-purple-100 mb-6">
                        Notre équipe est là pour vous accompagner dans l'intégration de l'API
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="mailto:<?= SITE_EMAIL ?>" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Support Email
                        </a>
                        <a href="#" class="bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-800 transition-colors">
                            <i class="fab fa-discord mr-2"></i>Discord Community
                        </a>
                        <a href="#" class="bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-800 transition-colors">
                            <i class="fab fa-github mr-2"></i>GitHub Issues
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    
    <script>
        // Menu mobile toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80; // Ajusté pour le header fixe
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Copier le code
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('copy-btn')) {
                const pre = e.target.closest('div').querySelector('pre code');
                const text = pre.textContent;
                
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = e.target.textContent;
                    e.target.textContent = 'Copié!';
                    e.target.classList.add('bg-green-600');
                    
                    setTimeout(() => {
                        e.target.textContent = originalText;
                        e.target.classList.remove('bg-green-600');
                    }, 2000);
                }).catch(() => {
                    alert('Erreur lors de la copie');
                });
            }
        });

      document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".lg\\:w-64.flex-shrink-0");
  const nav = document.querySelector(".fixed-nav");
  if (!sidebar || !nav) return;

  const placeholder = document.createElement("div");
  placeholder.className = "sidebar-placeholder";

  const navHeight = nav.offsetHeight;

  const fixSidebar = () => {
    if (window.innerWidth > 1020) {
      if (window.scrollY > navHeight) {
        if (!sidebar.classList.contains("sidebar-fixed")) {
          // Crée le placeholder pour garder l'espace
          placeholder.style.width = sidebar.offsetWidth + "px";
          placeholder.style.height = sidebar.offsetHeight + "px";
          sidebar.parentNode.insertBefore(placeholder, sidebar);

          sidebar.style.width = sidebar.offsetWidth + "px";
          sidebar.classList.add("sidebar-fixed");
          sidebar.style.top = navHeight + "px";

          requestAnimationFrame(() => {
            sidebar.classList.add("active");
          });
        }
      } else {
        sidebar.classList.remove("active", "sidebar-fixed");
        sidebar.style.top = "";
        sidebar.style.width = "";
        if (placeholder.parentNode) placeholder.remove();
      }
    } else {
      sidebar.classList.remove("active", "sidebar-fixed");
      sidebar.style.top = "";
      sidebar.style.width = "";
      if (placeholder.parentNode) placeholder.remove();
    }
  };

  window.addEventListener("scroll", fixSidebar);
  window.addEventListener("resize", fixSidebar);
});

        // Highlight du menu actuel
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.sidebar-sticky a[href^="#"]');
            
            let currentSection = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120; // Ajusté pour le header fixe
                if (window.pageYOffset >= sectionTop) {
                    currentSection = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('bg-purple-50', 'text-purple-600');
                if (link.getAttribute('href') === '#' + currentSection) {
                    link.classList.add('bg-purple-50', 'text-purple-600');
                }
            });
        });
    </script>
</body>
</html>