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
    <title><?= SITE_NAME ?> - Téléchargeur YouTube Professionnel</title>
    <meta name="description" content="Téléchargez vos vidéos et audios YouTube facilement. Service professionnel avec API pour développeurs.">
    
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
                    <a href="index.php" class="text-purple-600 font-medium">Accueil</a>
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
                <a href="index.php" class="block text-purple-600 py-2 font-medium">Accueil</a>
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
                Téléchargez vos vidéos YouTube
                <span class="block text-purple-200">en quelques secondes</span>
            </h1>
            <p class="text-xl mb-8 text-purple-100 max-w-3xl mx-auto">
                Service professionnel de téléchargement YouTube avec API pour développeurs. 
                Rapide, sécurisé et toujours disponible.
            </p>
            
            <!-- Statistiques -->
            <div class="flex justify-center space-x-8 mb-12">
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['total_downloads']) ?></div>
                    <div class="text-purple-200">Téléchargements</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['total_users']) ?></div>
                    <div class="text-purple-200">Utilisateurs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">99.9%</div>
                    <div class="text-purple-200">Disponibilité</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section de téléchargement -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Téléchargez maintenant</h2>
                    <p class="text-gray-600 text-lg">
                        Collez votre lien YouTube ci-dessous et choisissez votre format préféré
                    </p>
                </div>

                <!-- Formulaire de téléchargement -->
                <div class="bg-white rounded-xl shadow-xl p-8">
                    <form id="downloadForm" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        
                        <!-- URL Input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-link mr-2"></i>URL YouTube
                            </label>
                            <input 
                                type="url" 
                                id="youtube_url" 
                                name="youtube_url" 
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <!-- Format Selection -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-file-audio mr-2"></i>Format
                                </label>
                                <select id="format" name="format" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="audio">Audio MP3</option>
                                    <option value="video" selected>Vidéo MP4</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-cog mr-2"></i>Qualité
                                </label>
                                <select id="quality" name="quality" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="best">Meilleure qualité</option>
                                    <option value="1080">1080p HD</option>
                                    <option value="720">720p HD</option>
                                    <option value="480">480p</option>
                                </select>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button 
                                type="button" 
                                id="previewBtn"
                                class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center"
                            >
                                <i class="fas fa-eye mr-2"></i>
                                Prévisualiser
                            </button>
                            <button 
                                type="submit"
                                class="flex-1 bg-purple-600 text-white py-3 px-6 rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center"
                            >
                                <i class="fas fa-download mr-2"></i>
                                Télécharger
                            </button>
                        </div>
                    </form>

                    <!-- Zone d'information -->
                    <div id="videoInfo" class="hidden mt-6 p-4 bg-gray-50 rounded-lg"></div>
                    
                    <!-- Loader -->
                    <div id="loader" class="hidden mt-6 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                        <p class="mt-2 text-gray-600">Traitement en cours...</p>
                    </div>
                </div>

                <!-- Informations pour visiteurs -->
                <div class="mt-8 bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>Informations importantes
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-700">
                        <div>
                            <strong>Visiteurs :</strong> Accès libre à tous les formats sans limite de téléchargements
                        </div>
                        <div>
                            <strong>Utilisateurs API :</strong> Quotas et restrictions selon le type de compte
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Avantages -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Pourquoi choisir <?= SITE_NAME ?> ?</h2>
                <p class="text-gray-600 text-lg">Une solution professionnelle pour tous vos besoins</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                    <div class="text-purple-600 text-3xl mb-4">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Ultra Rapide</h3>
                    <p class="text-gray-600">Téléchargements instantanés grâce à notre infrastructure optimisée</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                    <div class="text-purple-600 text-3xl mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">100% Sécurisé</h3>
                    <p class="text-gray-600">Vos données sont protégées avec un chiffrement de niveau entreprise</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                    <div class="text-purple-600 text-3xl mb-4">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">API Développeur</h3>
                    <p class="text-gray-600">Intégrez facilement nos services dans vos applications</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                    <div class="text-purple-600 text-3xl mb-4">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Multi-plateforme</h3>
                    <p class="text-gray-600">Compatible avec tous les appareils et navigateurs</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                    <div class="text-purple-600 text-3xl mb-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Disponible 24/7</h3>
                    <p class="text-gray-600">Service disponible en permanence, sans interruption</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                    <div class="text-purple-600 text-3xl mb-4">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Support Premium</h3>
                    <p class="text-gray-600">Équipe support dédiée pour vous accompagner</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Comment ça marche -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Comment ça marche ?</h2>
                <p class="text-gray-600 text-lg">Trois étapes simples pour télécharger vos contenus</p>
            </div>

            <div class="grid md:grid-cols-2 gap-12">
                <!-- Pour les visiteurs -->
                <div class="space-y-6">
                    <h3 class="text-2xl font-semibold text-purple-600 mb-6">
                        <i class="fas fa-user mr-2"></i>Pour les visiteurs
                    </h3>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-purple-100 rounded-full w-8 h-8 flex items-center justify-center font-bold text-purple-600">1</div>
                        <div>
                            <h4 class="font-semibold mb-2">Collez votre lien</h4>
                            <p class="text-gray-600">Copiez l'URL de votre vidéo YouTube dans le formulaire</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-purple-100 rounded-full w-8 h-8 flex items-center justify-center font-bold text-purple-600">2</div>
                        <div>
                            <h4 class="font-semibold mb-2">Choisissez le format</h4>
                            <p class="text-gray-600">Sélectionnez audio MP3 ou vidéo MP4 avec la qualité désirée</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-purple-100 rounded-full w-8 h-8 flex items-center justify-center font-bold text-purple-600">3</div>
                        <div>
                            <h4 class="font-semibold mb-2">Téléchargez instantanément</h4>
                            <p class="text-gray-600">Cliquez sur télécharger et récupérez votre fichier immédiatement</p>
                        </div>
                    </div>
                </div>

                <!-- Pour les utilisateurs API -->
                <div class="space-y-6">
                    <h3 class="text-2xl font-semibold text-green-600 mb-6">
                        <i class="fas fa-key mr-2"></i>Pour les utilisateurs API
                    </h3>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center font-bold text-green-600">1</div>
                        <div>
                            <h4 class="font-semibold mb-2">Créez votre compte</h4>
                            <p class="text-gray-600">Inscrivez-vous et obtenez automatiquement votre clé API</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center font-bold text-green-600">2</div>
                        <div>
                            <h4 class="font-semibold mb-2">Respectez les quotas</h4>
                            <p class="text-gray-600">Utilisez votre clé dans le respect de vos limites quotidiennes</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center font-bold text-green-600">3</div>
                        <div>
                            <h4 class="font-semibold mb-2">Intégrez dans vos apps</h4>
                            <p class="text-gray-600">Utilisez notre API REST pour automatiser vos téléchargements</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Témoignages -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Ce que disent nos utilisateurs</h2>
                <p class="text-gray-600 text-lg">Plus de <?= number_format($stats['total_users']) ?> utilisateurs nous font confiance</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Marie L.</h4>
                            <p class="text-sm text-gray-600">Créatrice de contenu</p>
                        </div>
                    </div>
                    <p class="text-gray-700">"Service exceptionnellement rapide ! J'utilise l'API pour mes projets et c'est parfait. Support client réactif."</p>
                    <div class="flex text-yellow-400 mt-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Thomas K.</h4>
                            <p class="text-sm text-gray-600">Développeur</p>
                        </div>
                    </div>
                    <p class="text-gray-700">"API très bien documentée et stable. J'ai intégré le service dans mon app mobile sans problème."</p>
                    <div class="flex text-yellow-400 mt-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Sophie R.</h4>
                            <p class="text-sm text-gray-600">Étudiante</p>
                        </div>
                    </div>
                    <p class="text-gray-700">"Interface super intuitive ! Je télécharge mes cours en ligne facilement et gratuitement."</p>
                    <div class="flex text-yellow-400 mt-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
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
                        <li><a href="help.php" class="hover:text-white">Support</a></li>
                        <li><a href="#" class="hover:text-white">Status</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Légal</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="cgu.php" class="hover:text-white">CGU</a></li>
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Menu mobile
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Gestion du formulaire
        const downloadForm = document.getElementById('downloadForm');
        const previewBtn = document.getElementById('previewBtn');
        const videoInfo = document.getElementById('videoInfo');
        const loader = document.getElementById('loader');

        // Prévisualisation
        previewBtn.addEventListener('click', async function() {
            const url = document.getElementById('youtube_url').value;
            
            if (!url) {
                alert('Veuillez saisir une URL YouTube');
                return;
            }

            loader.classList.remove('hidden');
            videoInfo.classList.add('hidden');

            try {
                const response = await axios.get('video_info.php', {
                    params: { url: url }
                });

                if (response.data.success) {
                    const data = response.data;
                    videoInfo.innerHTML = `
                        <div class="flex items-start space-x-4">
                            <img src="${data.thumbnail}" alt="Thumbnail" class="w-24 h-18 rounded object-cover">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg mb-2">${data.title}</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div><strong>Durée:</strong> ${data.duration}</div>
                                    <div><strong>Auteur:</strong> ${data.uploader}</div>
                                    <div><strong>Vues:</strong> ${data.view_count ? new Intl.NumberFormat().format(data.view_count) : 'N/A'}</div>
                                </div>
                                <div class="mt-3">
                                    <strong>Formats disponibles:</strong>
                                    <ul class="list-disc list-inside mt-1">
                                        ${data.formats.map(f => `<li>${f.type} - ${f.quality} (${f.size})</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                    videoInfo.classList.remove('hidden');
                } else {
                    alert('Erreur: ' + response.data.error);
                }
            } catch (error) {
                alert('Erreur lors de la récupération des informations');
                console.error(error);
            } finally {
                loader.classList.add('hidden');
            }
        });

        // Téléchargement
        downloadForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = formData.get('youtube_url');
            const format = formData.get('format');
            const quality = formData.get('quality');

            if (!url) {
                alert('Veuillez saisir une URL YouTube');
                return;
            }

            loader.classList.remove('hidden');

            try {
                // Créer un lien de téléchargement
                const downloadUrl = `download.php?url=${encodeURIComponent(url)}&format=${format}&quality=${quality}&csrf_token=${formData.get('csrf_token')}`;
                
                // Ouvrir le téléchargement dans un nouvel onglet
                window.open(downloadUrl, '_blank');
                
                setTimeout(() => {
                    loader.classList.add('hidden');
                }, 2000);

            } catch (error) {
                alert('Erreur lors du téléchargement');
                console.error(error);
                loader.classList.add('hidden');
            }
        });

        // Fonction pour récupérer les paramètres d'URL et remplir le formulaire
        function fillFormFromUrlParams() {
            const urlParams = new URLSearchParams(window.location.search);
    
            // Récupérer les paramètres
            const videoId = urlParams.get('video_id');
            const format = urlParams.get('format');
            const quality = urlParams.get('quality');
    
            // Si on a un video_id, construire l'URL YouTube complète
            if (videoId) {
                const youtubeUrl = `https://www.youtube.com/watch?v=${videoId}`;
                document.getElementById('youtube_url').value = youtubeUrl;
            }
    
            // Remplir le champ format si présent
            if (format && (format === 'audio' || format === 'video')) {
                document.getElementById('format').value = format;
        
                // Déclencher l'événement change pour masquer/afficher la qualité
                document.getElementById('format').dispatchEvent(new Event('change'));
            }
    
            // Remplir le champ qualité si présent et si format n'est pas audio
            if (quality && format !== 'audio') {
                const qualitySelect = document.getElementById('quality');
                const qualityOption = Array.from(qualitySelect.options).find(option => 
                    option.value === quality || option.value === quality.toString()
                );
        
                if (qualityOption) {
                    qualitySelect.value = qualityOption.value;
                }
            }
    
            // Nettoyer l'URL après avoir récupéré les paramètres (optionnel)
            if (videoId || format || quality) {
                // Supprimer les paramètres de l'URL sans recharger la page
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        // Appeler la fonction au chargement de la page
        document.addEventListener('DOMContentLoaded', fillFormFromUrlParams);

        // Masquer la qualité si format audio
        document.getElementById('format').addEventListener('change', function() {
            const qualitySelect = document.getElementById('quality');
            if (this.value === 'audio') {
                qualitySelect.style.display = 'none';
                qualitySelect.previousElementSibling.style.display = 'none';
            } else {
                qualitySelect.style.display = 'block';
                qualitySelect.previousElementSibling.style.display = 'block';
            }
        });
    </script>
</body>
</html>