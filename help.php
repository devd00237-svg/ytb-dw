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
    <title><?= SITE_NAME ?> - Support & Aide</title>
    <meta name="description" content="Centre d'aide et support technique de <?= SITE_NAME ?>. FAQ, guides et contact support.">
    
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

        .faq-item {
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .support-card {
            transition: all 0.3s ease;
        }
        
        .support-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
                Centre d'aide &
                <span class="block text-purple-200">Support</span>
            </h1>
            <p class="text-xl mb-8 text-purple-100 max-w-3xl mx-auto">
                Trouvez rapidement les réponses à vos questions ou contactez notre équipe support
            </p>
            
            <!-- Barre de recherche -->
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Recherchez dans notre base de connaissances..." 
                        class="w-full px-6 py-4 pr-12 rounded-lg text-gray-800 focus:outline-none focus:ring-4 focus:ring-purple-300"
                    >
                    <button class="absolute right-4 top-1/2 transform -translate-y-1/2 text-purple-600">
                        <i class="fas fa-search text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Options de support -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Comment pouvons-nous vous aider ?</h2>
                <p class="text-gray-600 text-lg">Choisissez la méthode qui vous convient le mieux</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-lg p-8 support-card text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-question-circle text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">FAQ</h3>
                    <p class="text-gray-600 mb-6">Consultez nos questions fréquemment posées</p>
                    <button onclick="scrollToFAQ()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Voir la FAQ
                    </button>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8 support-card text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Email Support</h3>
                    <p class="text-gray-600 mb-6">Contactez notre équipe par email</p>
                    <button onclick="scrollToContact()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                        Nous écrire
                    </button>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8 support-card text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-book text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Documentation</h3>
                    <p class="text-gray-600 mb-6">Guides complets et API reference</p>
                    <a href="docs.php" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                        Voir les docs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques du support -->
    <section class="py-12 bg-gray-100">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-purple-600 mb-2">< 2h</div>
                    <div class="text-gray-600">Temps de réponse moyen</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-green-600 mb-2">99.2%</div>
                    <div class="text-gray-600">Satisfaction client</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-blue-600 mb-2">24/7</div>
                    <div class="text-gray-600">Support disponible</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600 mb-2"><?= number_format($stats['total_users']) ?></div>
                    <div class="text-gray-600">Utilisateurs aidés</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq-section" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Questions Fréquentes</h2>
                    <p class="text-gray-600 text-lg">Les réponses aux questions les plus posées</p>
                </div>

                <!-- Catégories FAQ -->
                <div class="flex flex-wrap justify-center gap-4 mb-8">
                    <button onclick="filterFAQ('all')" class="faq-filter active bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        Toutes
                    </button>
                    <button onclick="filterFAQ('general')" class="faq-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Général
                    </button>
                    <button onclick="filterFAQ('api')" class="faq-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        API
                    </button>
                    <button onclick="filterFAQ('account')" class="faq-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Compte
                    </button>
                    <button onclick="filterFAQ('technical')" class="faq-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Technique
                    </button>
                </div>

                <!-- Questions FAQ -->
                <div class="space-y-4" id="faq-container">
                    <!-- Général -->
                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="general" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Comment fonctionne <?= SITE_NAME ?> ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p><?= SITE_NAME ?> vous permet de télécharger facilement des vidéos et audios depuis YouTube. Il suffit de coller l'URL de la vidéo, choisir le format et qualité désirés, puis cliquer sur télécharger. Le service est gratuit pour les visiteurs sans limitation.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="general" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Y a-t-il des limitations pour les visiteurs ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Non ! Les visiteurs ont un accès libre et illimité à tous les formats de téléchargement via l'interface web. Aucune inscription n'est requise et il n'y a pas de quota de téléchargements.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="general" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Quels formats sont supportés ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Nous supportons les formats MP4 (vidéo) en qualités 1080p, 720p, 480p et MP3 (audio) haute qualité. Tous les formats populaires de YouTube sont compatibles.</p>
                        </div>
                    </div>

                    <!-- API -->
                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="api" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Comment obtenir une clé API ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Créez simplement un compte gratuit sur notre site. Votre clé API sera automatiquement générée et accessible dans votre dashboard. La clé API gratuite offre 100 requêtes par jour.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="api" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Quelles sont les limites de l'API gratuite ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>L'API gratuite permet 100 requêtes par jour avec accès aux formats standards. L'API Premium offre 10,000 requêtes quotidiennes, tous les formats, et un support prioritaire.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="api" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Comment utiliser l'API ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Consultez notre documentation complète dans la section "Documentation". Vous y trouverez tous les endpoints, exemples de code et bonnes pratiques pour intégrer notre API.</p>
                        </div>
                    </div>

                    <!-- Compte -->
                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="account" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Dois-je créer un compte pour télécharger ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Non, pas du tout ! Vous pouvez utiliser librement notre service de téléchargement sans créer de compte. L'inscription n'est nécessaire que si vous souhaitez utiliser notre API pour vos projets.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="account" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Comment supprimer mon compte ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Vous pouvez supprimer votre compte depuis votre dashboard ou en nous contactant. Toutes vos données seront supprimées conformément au RGPD. Consultez notre page RGPD pour plus d'infos.</p>
                        </div>
                    </div>

                    <!-- Technique -->
                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="technical" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Pourquoi mon téléchargement échoue ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Vérifiez que l'URL YouTube est correcte et que la vidéo n'est pas privée ou géo-restreinte. Si le problème persiste, essayez un autre format ou contactez notre support.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-gray-50 rounded-lg p-6 cursor-pointer" data-category="technical" onclick="toggleFAQ(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Vos serveurs sont-ils sécurisés ?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transform transition-transform"></i>
                        </div>
                        <div class="faq-answer hidden mt-4 text-gray-700">
                            <p>Oui, nous utilisons un chiffrement SSL/TLS, des pare-feu avancés et nos serveurs sont hébergés en France chez OVH avec protection DDoS par Cloudflare. Vos données sont protégées selon les standards européens.</p>
                        </div>
                    </div>
                </div>

                <!-- Pas trouvé votre réponse -->
                <div class="text-center mt-12">
                    <div class="bg-blue-50 rounded-lg p-8">
                        <h3 class="text-xl font-semibold text-blue-800 mb-4">Pas trouvé votre réponse ?</h3>
                        <p class="text-blue-700 mb-6">Notre équipe support est là pour vous aider</p>
                        <button onclick="scrollToContact()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Contactez-nous
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulaire de contact -->
    <section id="contact-section" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Contactez notre support</h2>
                    <p class="text-gray-600 text-lg">Nous répondons généralement sous 2 heures</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Formulaire -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <form id="supportForm" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            
                            <!-- Type de demande -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2"></i>Type de demande
                                </label>
                                <select name="support_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="">Sélectionnez un type</option>
                                    <option value="technical">Problème technique</option>
                                    <option value="account">Question sur mon compte</option>
                                    <option value="api">Support API</option>
                                    <option value="billing">Facturation</option>
                                    <option value="feature">Demande de fonctionnalité</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>

                            <!-- Priorité -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Priorité
                                </label>
                                <select name="priority" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="low">Faible - Question générale</option>
                                    <option value="medium" selected>Moyenne - Problème standard</option>
                                    <option value="high">Élevée - Problème bloquant</option>
                                    <option value="urgent">Urgente - Service inaccessible</option>
                                </select>
                            </div>

                            <!-- Nom et Email -->
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                                    <input 
                                        type="text" 
                                        name="full_name" 
                                        required
                                        <?php if (isset($_SESSION['user_name'])): ?>
                                            value="<?= htmlspecialchars($_SESSION['user_name']) ?>"
                                        <?php endif; ?>
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        required
                                        <?php if (isset($_SESSION['user_email'])): ?>
                                            value="<?= htmlspecialchars($_SESSION['user_email']) ?>"
                                        <?php endif; ?>
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                    >
                                </div>
                            </div>

                            <!-- Sujet -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-heading mr-2"></i>Sujet
                                </label>
                                <input 
                                    type="text" 
                                    name="subject" 
                                    required
                                    placeholder="Résumez votre problème en quelques mots"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                >
                            </div>

                            <!-- Message -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-comment mr-2"></i>Description détaillée
                                </label>
                                <textarea 
                                    name="message" 
                                    rows="6" 
                                    required
                                    placeholder="Décrivez votre problème en détail. N'hésitez pas à inclure des étapes pour reproduire le problème, messages d'erreur, etc."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                ></textarea>
                            </div>

                            <!-- Informations système (pour support technique) -->
                            <div id="system-info" class="hidden bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3">
                                    <i class="fas fa-desktop mr-2"></i>Informations système (optionnel)
                                </h4>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Navigateur</label>
                                        <input 
                                            type="text" 
                                            name="browser" 
                                            placeholder="Chrome, Firefox, Safari..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Système d'exploitation</label>
                                        <input 
                                            type="text" 
                                            name="os" 
                                            placeholder="Windows, macOS, Linux..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Fichier joint -->
                            <div class="bg-blue-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-blue-800 mb-2">
                                    <i class="fas fa-paperclip mr-2"></i>Fichier joint (optionnel)
                                </label>
                                <input 
                                    type="file" 
                                    name="attachment" 
                                    accept=".jpg,.jpeg,.png,.pdf,.txt,.log"
                                    class="w-full px-3 py-2 border border-blue-300 rounded-lg bg-white text-sm"
                                >
                                <p class="text-blue-700 text-sm mt-2">
                                    Formats acceptés : JPG, PNG, PDF, TXT, LOG (max 10MB)
                                </p>
                            </div>

                            <!-- Submit -->
                            <button 
                                type="submit" 
                                class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center"
                            >
                                <i class="fas fa-paper-plane mr-2"></i>
                                Envoyer ma demande
                            </button>
                        </form>
                    </div>

                    <!-- Informations de contact -->
                    <div class="space-y-6">
                        <!-- Temps de réponse -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                                <i class="fas fa-clock mr-2 text-green-600"></i>Temps de réponse
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Faible</span>
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">24-48h</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Moyenne</span>
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">4-8h</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Élevée</span>
                                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm">1-2h</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Urgente</span>
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">< 30min</span>
                                </div>
                            </div>
                        </div>

                        <!-- Autres moyens de contact -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                                <i class="fas fa-phone mr-2 text-blue-600"></i>Autres moyens de contact
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-purple-600 w-6 mr-3"></i>
                                    <div>
                                        <p class="font-medium">Email direct</p>
                                        <p class="text-gray-600 text-sm">support@<?= strtolower(str_replace(' ', '', SITE_NAME)) ?>.com</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-green-600 w-6 mr-3"></i>
                                    <div>
                                        <p class="font-medium">Téléphone</p>
                                        <p class="text-gray-600 text-sm">+33 1 23 45 67 89 (9h-17h)</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <i class="fab fa-discord text-indigo-600 w-6 mr-3"></i>
                                    <div>
                                        <p class="font-medium">Discord</p>
                                        <p class="text-gray-600 text-sm">Communauté support</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <i class="fab fa-twitter text-blue-400 w-6 mr-3"></i>
                                    <div>
                                        <p class="font-medium">Twitter</p>
                                        <p class="text-gray-600 text-sm">@<?= strtolower(str_replace(' ', '', SITE_NAME)) ?>_support</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Horaires support -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                                <i class="fas fa-calendar mr-2 text-orange-600"></i>Horaires du support
                            </h3>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Lundi - Vendredi</span>
                                    <span class="font-medium">9h00 - 19h00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Samedi</span>
                                    <span class="font-medium">10h00 - 16h00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Dimanche</span>
                                    <span class="font-medium text-gray-500">Fermé</span>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex items-center text-green-600">
                                        <i class="fas fa-circle text-xs mr-2"></i>
                                        <span class="text-sm">Support d'urgence 24/7 disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status système -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                                <i class="fas fa-heartbeat mr-2 text-red-600"></i>Status du système
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">API Service</span>
                                    <div class="flex items-center">
                                        <i class="fas fa-circle text-green-500 text-xs mr-2"></i>
                                        <span class="text-green-600 text-sm">Opérationnel</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Interface Web</span>
                                    <div class="flex items-center">
                                        <i class="fas fa-circle text-green-500 text-xs mr-2"></i>
                                        <span class="text-green-600 text-sm">Opérationnel</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Serveurs de téléchargement</span>
                                    <div class="flex items-center">
                                        <i class="fas fa-circle text-green-500 text-xs mr-2"></i>
                                        <span class="text-green-600 text-sm">Opérationnel</span>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="status.php" class="text-purple-600 hover:text-purple-700 text-sm">
                                        Voir la page status complète →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ressources utiles -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Ressources utiles</h2>
                <p class="text-gray-600 text-lg">Trouvez rapidement ce dont vous avez besoin</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="docs.php" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-book text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Documentation API</h3>
                    <p class="text-gray-600 text-sm">Guide complet pour intégrer notre API</p>
                </a>

                <a href="status.php" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Status des services</h3>
                    <p class="text-gray-600 text-sm">Vérifiez la disponibilité en temps réel</p>
                </a>

                <a href="cgu.php" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-file-contract text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">CGU et Politique</h3>
                    <p class="text-gray-600 text-sm">Conditions d'utilisation et confidentialité</p>
                </a>

                <a href="rgpd.php" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Données RGPD</h3>
                    <p class="text-gray-600 text-sm">Gérez vos données personnelles</p>
                </a>
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
                        <li><a href="support.php" class="text-purple-400">Support</a></li>
                        <li><a href="status.php" class="hover:text-white">Status</a></li>
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

        // Fonctions de navigation
        function scrollToFAQ() {
            document.getElementById('faq-section').scrollIntoView({ behavior: 'smooth' });
        }

        function scrollToContact() {
            document.getElementById('contact-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Recherche dans la FAQ
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const title = item.querySelector('h3').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                
                if (title.includes(query) || answer.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = query ? 'none' : 'block';
                }
            });
        });

        // Filtrage FAQ par catégorie
        function filterFAQ(category) {
            const faqItems = document.querySelectorAll('.faq-item');
            const filters = document.querySelectorAll('.faq-filter');
            
            // Mise à jour des boutons
            filters.forEach(btn => {
                btn.classList.remove('active', 'bg-purple-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            event.target.classList.remove('bg-gray-200', 'text-gray-700');
            event.target.classList.add('active', 'bg-purple-600', 'text-white');
            
            // Filtrage des éléments
            faqItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Toggle FAQ réponse
        function toggleFAQ(element) {
            const answer = element.querySelector('.faq-answer');
            const icon = element.querySelector('i');
            
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                answer.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Gestion formulaire support
        document.getElementById('supportForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Loader
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
            
            try {
                const formData = new FormData(this);
                
                const response = await axios.post('process_support.php', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                if (response.data.success) {
                    // Succès
                    alert('Votre demande de support a été envoyée avec succès. Vous recevrez une réponse selon la priorité sélectionnée.');
                    this.reset();
                    document.getElementById('system-info').classList.add('hidden');
                } else {
                    alert('Erreur: ' + response.data.error);
                }
            } catch (error) {
                alert('Erreur lors de l\'envoi de la demande. Veuillez réessayer.');
                console.error(error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Afficher/masquer les infos système selon le type
        document.querySelector('select[name="support_type"]').addEventListener('change', function() {
            const systemInfo = document.getElementById('system-info');
            if (this.value === 'technical') {
                systemInfo.classList.remove('hidden');
            } else {
                systemInfo.classList.add('hidden');
            }
        });

        // Auto-détection informations système
        if (navigator.userAgent) {
            const ua = navigator.userAgent;
            let browser = 'Inconnu';
            let os = 'Inconnu';
            
            // Détection navigateur
            if (ua.includes('Chrome')) browser = 'Chrome';
            else if (ua.includes('Firefox')) browser = 'Firefox';
            else if (ua.includes('Safari')) browser = 'Safari';
            else if (ua.includes('Edge')) browser = 'Edge';
            
            // Détection OS
            if (ua.includes('Windows')) os = 'Windows';
            else if (ua.includes('Mac')) os = 'macOS';
            else if (ua.includes('Linux')) os = 'Linux';
            else if (ua.includes('Android')) os = 'Android';
            else if (ua.includes('iOS')) os = 'iOS';
            
            // Pré-remplir les champs
            document.querySelector('input[name="browser"]').placeholder = browser;
            document.querySelector('input[name="os"]').placeholder = os;
        }
    </script>
</body>
</html>