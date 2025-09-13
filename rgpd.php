<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - RGPD & Gestion des Données</title>
    <meta name="description" content="Exercez vos droits RGPD sur <?= SITE_NAME ?>. Accédez, modifiez ou supprimez vos données personnelles.">
    
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

        .form-section {
            transition: all 0.3s ease;
        }
        
        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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
                Gestion de vos
                <span class="block text-purple-200">Données Personnelles</span>
            </h1>
            <p class="text-xl mb-8 text-purple-100 max-w-3xl mx-auto">
                Exercez vos droits RGPD en toute simplicité. Accédez, modifiez ou supprimez vos données.
            </p>
            <div class="flex justify-center items-center space-x-4 text-purple-200">
                <i class="fas fa-shield-alt text-2xl"></i>
                <span>Conformité RGPD garantie</span>
            </div>
        </div>
    </section>

    <!-- Contenu principal -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="max-w-6xl mx-auto">

                <!-- Vos droits -->
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Vos droits selon le RGPD</h2>
                    
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-green-50 rounded-lg p-6 form-section">
                            <div class="text-green-600 text-3xl mb-4">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-green-800">Droit d'accès</h3>
                            <p class="text-green-700 mb-4">Consultez toutes les données que nous détenons sur vous</p>
                            <ul class="text-green-600 text-sm space-y-1">
                                <li>• Informations de compte</li>
                                <li>• Historique d'utilisation</li>
                                <li>• Données techniques</li>
                            </ul>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-6 form-section">
                            <div class="text-blue-600 text-3xl mb-4">
                                <i class="fas fa-edit"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-blue-800">Droit de rectification</h3>
                            <p class="text-blue-700 mb-4">Corrigez ou mettez à jour vos informations</p>
                            <ul class="text-blue-600 text-sm space-y-1">
                                <li>• Email et coordonnées</li>
                                <li>• Préférences utilisateur</li>
                                <li>• Informations incorrectes</li>
                            </ul>
                        </div>

                        <div class="bg-red-50 rounded-lg p-6 form-section">
                            <div class="text-red-600 text-3xl mb-4">
                                <i class="fas fa-trash"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-red-800">Droit à l'effacement</h3>
                            <p class="text-red-700 mb-4">Supprimez définitivement vos données (droit à l'oubli)</p>
                            <ul class="text-red-600 text-sm space-y-1">
                                <li>• Compte complet</li>
                                <li>• Historique d'activité</li>
                                <li>• Données personnelles</li>
                            </ul>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-6 form-section">
                            <div class="text-purple-600 text-3xl mb-4">
                                <i class="fas fa-download"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-purple-800">Droit à la portabilité</h3>
                            <p class="text-purple-700 mb-4">Exportez vos données dans un format exploitable</p>
                            <ul class="text-purple-600 text-sm space-y-1">
                                <li>• Format JSON/CSV</li>
                                <li>• Données structurées</li>
                                <li>• Fichier téléchargeable</li>
                            </ul>
                        </div>

                        <div class="bg-orange-50 rounded-lg p-6 form-section">
                            <div class="text-orange-600 text-3xl mb-4">
                                <i class="fas fa-pause"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-orange-800">Droit de limitation</h3>
                            <p class="text-orange-700 mb-4">Suspendez temporairement le traitement</p>
                            <ul class="text-orange-600 text-sm space-y-1">
                                <li>• Traitement suspendu</li>
                                <li>• Conservation limitée</li>
                                <li>• Notification préalable</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-6 form-section">
                            <div class="text-yellow-600 text-3xl mb-4">
                                <i class="fas fa-ban"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-yellow-800">Droit d'opposition</h3>
                            <p class="text-yellow-700 mb-4">Opposez-vous au traitement pour motif légitime</p>
                            <ul class="text-yellow-600 text-sm space-y-1">
                                <li>• Marketing direct</li>
                                <li>• Profilage publicitaire</li>
                                <li>• Intérêt légitime contesté</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Formulaires d'exercice des droits -->
                <div class="grid lg:grid-cols-2 gap-8">
                    
                    <!-- Formulaire principal -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">
                            <i class="fas fa-user-shield mr-2 text-indigo-600"></i>Exercer vos droits RGPD
                        </h2>
                        
                        <form id="rgpdForm" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            
                            <!-- Type de demande -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list mr-2"></i>Type de demande
                                </label>
                                <select name="request_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option value="">Choisissez votre demande</option>
                                    <option value="access">Droit d'accès - Consulter mes données</option>
                                    <option value="rectification">Droit de rectification - Corriger mes données</option>
                                    <option value="erasure">Droit à l'effacement - Supprimer mes données</option>
                                    <option value="portability">Droit à la portabilité - Exporter mes données</option>
                                    <option value="restriction">Droit de limitation - Limiter le traitement</option>
                                    <option value="objection">Droit d'opposition - M'opposer au traitement</option>
                                </select>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2"></i>Adresse email
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    required 
                                    placeholder="votre.email@exemple.com"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                >
                                <p class="text-sm text-gray-600 mt-1">Email associé à votre compte (si applicable)</p>
                            </div>

                            <!-- Nom complet -->
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                                    <input 
                                        type="text" 
                                        name="first_name" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                                    <input 
                                        type="text" 
                                        name="last_name" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-comment mr-2"></i>Description de votre demande
                                </label>
                                <textarea 
                                    name="description" 
                                    rows="4" 
                                    placeholder="Décrivez précisément votre demande..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                ></textarea>
                            </div>

                            <!-- Justification -->
                            <div id="justification_section" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-balance-scale mr-2"></i>Justification (pour opposition/limitation)
                                </label>
                                <textarea 
                                    name="justification" 
                                    rows="3" 
                                    placeholder="Motifs légitimes justifiant votre demande..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                ></textarea>
                            </div>

                            <!-- Upload pièce d'identité -->
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-yellow-800 mb-2">
                                    <i class="fas fa-id-card mr-2"></i>Pièce d'identité (obligatoire)
                                </label>
                                <input 
                                    type="file" 
                                    name="identity_document" 
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="w-full px-4 py-2 border border-yellow-300 rounded-lg bg-white"
                                >
                                <p class="text-yellow-700 text-sm mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Formats acceptés : JPG, PNG, PDF (max 5MB). Nécessaire pour vérifier votre identité.
                                </p>
                            </div>

                            <!-- Consentement RGPD -->
                            <div class="bg-blue-50 rounded-lg p-4">
                                <label class="flex items-start">
                                    <input type="checkbox" name="consent" required class="mt-1 mr-3">
                                    <span class="text-blue-800 text-sm">
                                        J'accepte que mes données soient traitées uniquement dans le cadre de cette demande RGPD. 
                                        Mes informations seront supprimées après traitement de ma demande 
                                        (délai légal de conservation : 3 ans).
                                    </span>
                                </label>
                            </div>

                            <!-- Submit -->
                            <button 
                                type="submit" 
                                class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center"
                            >
                                <i class="fas fa-paper-plane mr-2"></i>
                                Soumettre ma demande
                            </button>
                        </form>
                    </div>

                    <!-- Informations et guide -->
                    <div class="space-y-6">
                        <!-- Délais et procédure -->
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-green-800 mb-4">
                                <i class="fas fa-clock mr-2"></i>Délais et procédure
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center mr-3 mt-1">
                                        <span class="text-green-600 font-bold text-sm">1</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-green-800">Accusé de réception</h4>
                                        <p class="text-green-700 text-sm">Sous 48h ouvrées</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center mr-3 mt-1">
                                        <span class="text-green-600 font-bold text-sm">2</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-green-800">Vérification identité</h4>
                                        <p class="text-green-700 text-sm">Analyse de votre pièce d'identité</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center mr-3 mt-1">
                                        <span class="text-green-600 font-bold text-sm">3</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-green-800">Traitement</h4>
                                        <p class="text-green-700 text-sm">Réponse sous 1 mois maximum</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Types de données collectées -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-blue-800 mb-4">
                                <i class="fas fa-database mr-2"></i>Données que nous traitons
                            </h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <h4 class="font-medium text-blue-800">Visiteurs (sans compte)</h4>
                                    <ul class="text-blue-700 text-sm space-y-1 mt-1">
                                        <li>• Adresse IP (anonymisée)</li>
                                        <li>• Données de navigation</li>
                                        <li>• URLs soumises (supprimées)</li>
                                    </ul>
                                </div>
                                
                                <div>
                                    <h4 class="font-medium text-blue-800">Comptes API</h4>
                                    <ul class="text-blue-700 text-sm space-y-1 mt-1">
                                        <li>• Email et identifiants</li>
                                        <li>• Historique des requêtes</li>
                                        <li>• Statistiques d'usage</li>
                                        <li>• Préférences utilisateur</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Contact DPO -->
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-purple-800 mb-4">
                                <i class="fas fa-user-tie mr-2"></i>Notre DPO
                            </h3>
                            
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-purple-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-purple-800">Ralph Urgue</h4>
                                    <p class="text-purple-600 text-sm">Déléguée à la Protection des Données</p>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-purple-700 text-sm">
                                <p><i class="fas fa-envelope mr-2"></i>dpo@<?= strtolower(str_replace('https://', '', SITE_URL)) ?></p>
                                <p><i class="fas fa-phone mr-2"></i>+237 6 xx xx xx xx</p>
                                <p><i class="fas fa-clock mr-2"></i>Lun-Ven : 9h-17h</p>
                            </div>
                        </div>

                        <!-- Réclamation CNIL 
                        <div class="bg-red-50 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-red-800 mb-4">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Réclamation CNIL
                            </h3>
                            
                            <p class="text-red-700 text-sm mb-3">
                                Si vous n'êtes pas satisfait de notre réponse, vous pouvez saisir la CNIL :
                            </p>
                            
                            <div class="space-y-2 text-red-700 text-sm">
                                <p><strong>En ligne :</strong> www.cnil.fr</p>
                                <p><strong>Courrier :</strong> 3 Place de Fontenoy, 75334 Paris Cedex 07</p>
                                <p><strong>Téléphone :</strong> 01 53 73 22 22</p>
                            </div>
                        </div>-->
                    </div>
                </div>

                <!-- Statut des demandes (si connecté) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mt-12 bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-history mr-2 text-orange-600"></i>Mes demandes RGPD
                    </h2>
                    
                    <div id="user-requests">
                        <!-- Contenu chargé via JavaScript -->
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">Chargement de vos demandes...</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

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
                        <li><a href="privacy.php" class="hover:text-white">Politique de confidentialité</a></li>
                        <li><a href="rgpd.php" class="text-purple-400">RGPD</a></li>
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

        // Gestion du formulaire RGPD
        const rgpdForm = document.getElementById('rgpdForm');
        const requestTypeSelect = rgpdForm.querySelector('select[name="request_type"]');
        const justificationSection = document.getElementById('justification_section');

        // Afficher la justification si nécessaire
        requestTypeSelect.addEventListener('change', function() {
            if (this.value === 'objection' || this.value === 'restriction') {
                justificationSection.classList.remove('hidden');
                justificationSection.querySelector('textarea').required = true;
            } else {
                justificationSection.classList.add('hidden');
                justificationSection.querySelector('textarea').required = false;
            }
        });

        // Soumission du formulaire
        rgpdForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Loader
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
            
            try {
                const formData = new FormData(this);
                
                const response = await axios.post('process_rgpd.php', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                if (response.data.success) {
                    // Succès
                    alert('Votre demande RGPD a été envoyée avec succès. Vous recevrez un accusé de réception par email sous 48h.');
                    this.reset();
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

        // Charger les demandes utilisateur si connecté
        <?php if (isset($_SESSION['user_id'])): ?>
        async function loadUserRequests() {
            try {
                const response = await axios.get('get_user_rgpd_requests.php');
                const requests = response.data.requests;
                const container = document.getElementById('user-requests');
                
                if (requests.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Aucune demande</h3>
                            <p class="text-gray-500">Vous n'avez pas encore effectué de demande RGPD.</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="space-y-4">';
                requests.forEach(request => {
                    const statusClass = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'processing': 'bg-blue-100 text-blue-800',
                        'completed': 'bg-green-100 text-green-800',
                        'rejected': 'bg-red-100 text-red-800'
                    };
                    
                    const statusIcon = {
                        'pending': 'fa-clock',
                        'processing': 'fa-spinner fa-spin',
                        'completed': 'fa-check-circle',
                        'rejected': 'fa-times-circle'
                    };
                    
                    const statusText = {
                        'pending': 'En attente',
                        'processing': 'En cours',
                        'completed': 'Terminé',
                        'rejected': 'Rejeté'
                    };
                    
                    html += `
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800">${request.type_label}</h4>
                                    <p class="text-gray-600 text-sm">Demande #${request.id}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass[request.status]}">
                                    <i class="fas ${statusIcon[request.status]} mr-1"></i>
                                    ${statusText[request.status]}
                                </span>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <strong>Date de demande:</strong> ${new Date(request.created_at).toLocaleDateString('fr-FR')}
                                </div>
                                <div>
                                    <strong>Dernière mise à jour:</strong> ${new Date(request.updated_at).toLocaleDateString('fr-FR')}
                                </div>
                            </div>
                            
                            ${request.description ? `
                                <div class="mt-3">
                                    <strong>Description:</strong>
                                    <p class="text-gray-700 text-sm mt-1">${request.description}</p>
                                </div>
                            ` : ''}
                            
                            ${request.response ? `
                                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                                    <strong class="text-gray-800">Réponse de notre équipe:</strong>
                                    <p class="text-gray-700 text-sm mt-2">${request.response}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
                html += '</div>';
                
                container.innerHTML = html;
            } catch (error) {
                console.error('Erreur lors du chargement des demandes:', error);
                document.getElementById('user-requests').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-400 mb-3"></i>
                        <p class="text-red-600">Erreur lors du chargement de vos demandes</p>
                    </div>
                `;
            }
        }
        
        // Charger au démarrage
        loadUserRequests();
        <?php endif; ?>
    </script>
</body>
</html>