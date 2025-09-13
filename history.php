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

// Paramètres de filtrage
$format = $_GET['format'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$search = trim($_GET['search'] ?? '');

// Construction de la requête avec filtres
$whereConditions = ['d.user_id = ?'];
$params = [$_SESSION['user_id']];

if ($format && in_array($format, ['audio', 'video'])) {
    $whereConditions[] = 'd.format = ?';
    $params[] = $format;
}

if ($dateFrom) {
    $whereConditions[] = 'DATE(d.download_date) >= ?';
    $params[] = $dateFrom;
}

if ($dateTo) {
    $whereConditions[] = 'DATE(d.download_date) <= ?';
    $params[] = $dateTo;
}

if ($search) {
    $whereConditions[] = '(d.video_title LIKE ? OR d.video_id LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $whereConditions);

// GESTION DE L'EXPORT CSV - AVANT TOUT OUTPUT HTML
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // Récupérer tous les téléchargements avec les mêmes filtres
    $exportStmt = $db->prepare("
        SELECT d.video_id, d.video_title, d.format, d.quality, d.file_size, d.download_date,
               DATE_FORMAT(d.download_date, '%d/%m/%Y %H:%i:%s') as formatted_date
        FROM downloads d 
        WHERE $whereClause
        ORDER BY d.download_date DESC
    ");
    $exportStmt->execute($params);
    $exportData = $exportStmt->fetchAll();

    // Définir les en-têtes pour le téléchargement CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="historique_telechargements_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Créer le fichier CSV
    $output = fopen('php://output', 'w');
    
    // BOM pour UTF-8 (pour Excel)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // En-têtes CSV
    fputcsv($output, [
        'ID Vidéo',
        'Titre',
        'Format',
        'Qualité',
        'Taille (octets)',
        'Date de téléchargement',
        'Lien YouTube'
    ], ';');
    
    // Données
    foreach ($exportData as $row) {
        fputcsv($output, [
            $row['video_id'],
            $row['video_title'] ?: 'Titre indisponible',
            ucfirst($row['format']),
            $row['quality'] ? $row['quality'] . 'p' : '',
            $row['file_size'] ?: '',
            $row['formatted_date'],
            'https://youtube.com/watch?v=' . $row['video_id']
        ], ';');
    }
    
    fclose($output);
    exit; // Arrêter l'exécution ici pour éviter tout output HTML
}

// Paramètres de pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Compter le nombre total d'enregistrements
$countStmt = $db->prepare("
    SELECT COUNT(*) as total
    FROM downloads d 
    WHERE $whereClause
");
$countStmt->execute($params);
$totalRecords = $countStmt->fetch()['total'];
$totalPages = ceil($totalRecords / $perPage);

// Récupérer les téléchargements avec pagination
$stmt = $db->prepare("
    SELECT d.*, 
           DATE_FORMAT(d.download_date, '%d/%m/%Y à %H:%i') as formatted_date,
           DATE_FORMAT(d.download_date, '%Y-%m-%d') as date_only
    FROM downloads d 
    WHERE $whereClause
    ORDER BY d.download_date DESC 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$downloads = $stmt->fetchAll();

// Statistiques pour les filtres
$statsStmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN format = 'audio' THEN 1 END) as audio_count,
        COUNT(CASE WHEN format = 'video' THEN 1 END) as video_count,
        MIN(DATE(download_date)) as first_download,
        MAX(DATE(download_date)) as last_download
    FROM downloads 
    WHERE user_id = ?
");
$statsStmt->execute([$_SESSION['user_id']]);
$stats = $statsStmt->fetch();

// Fonction utilitaire pour calculer le temps écoulé
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'À l\'instant';
    if ($time < 3600) return floor($time/60) . ' min';
    if ($time < 86400) return floor($time/3600) . ' h';
    if ($time < 2592000) return floor($time/86400) . ' j';
    if ($time < 31536000) return floor($time/2592000) . ' mois';
    
    return floor($time/31536000) . ' ans';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des téléchargements - <?= SITE_NAME ?></title>
    <meta name="description" content="Consultez l'historique complet de vos téléchargements avec filtres et recherche.">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            body {
                padding-top: 64px;
            }
        }

        /* Animation pour les lignes de l'historique */
        .history-row {
            transition: all 0.2s ease;
        }
        .history-row:hover {
            background-color: #f9fafb;
            transform: translateX(2px);
        }

        /* Styles pour le pagination */
        .pagination-btn {
            transition: all 0.2s ease;
        }
        .pagination-btn:hover:not(.disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
                        <div class="px-2 py-1 bg-<?= $user['type_compte'] === 'gratuit' ? 'green' : ($user['type_compte'] === 'premium' ? 'purple' : 'yellow') ?>-100 text-<?= $user['type_compte'] === 'gratuit' ? 'green' : ($user['type_compte'] === 'premium' ? 'purple' : 'yellow') ?>-800 text-xs rounded-full">
                            <?= ucfirst($user['type_compte']) ?>
                        </div>
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
        
        <!-- Menu mobile -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-4 sm:px-6 py-3 space-y-2">
                <a href="index.php" class="block text-gray-700 py-2">Accueil</a>
                <a href="dashboard.php" class="block text-gray-700 py-2">Dashboard</a>
                <a href="docs.php" class="block text-gray-700 py-2">Documentation</a>
                <div class="flex items-center py-2">
                    <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-user text-purple-600 text-xs"></i>
                    </div>
                    <span class="text-sm text-gray-700"><?= e($user['email']) ?></span>
                </div>
                <a href="logout.php" class="block text-red-600 py-2">Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-2">
                        <a href="dashboard.php" class="text-purple-200 hover:text-white mr-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-3xl font-bold">Historique des téléchargements</h1>
                    </div>
                    <p class="text-purple-100">
                        <?= number_format($totalRecords) ?> téléchargement<?= $totalRecords > 1 ? 's' : '' ?> au total
                    </p>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-purple-100">Période</p>
                    <p class="text-white font-semibold">
                        <?php if ($stats['first_download'] && $stats['last_download']): ?>
                            <?= date('d/m/Y', strtotime($stats['first_download'])) ?> - <?= date('d/m/Y', strtotime($stats['last_download'])) ?>
                        <?php else: ?>
                            Aucun téléchargement
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container mx-auto px-6 py-8">
        <!-- Statistiques rapides -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-download text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?= number_format($stats['total']) ?></h3>
                        <p class="text-gray-600 text-sm">Total téléchargements</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-file-audio text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?= number_format($stats['audio_count']) ?></h3>
                        <p class="text-gray-600 text-sm">Fichiers audio</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-file-video text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?= number_format($stats['video_count']) ?></h3>
                        <p class="text-gray-600 text-sm">Fichiers vidéo</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg p-6 shadow-lg mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-filter mr-2 text-purple-600"></i>Filtres et recherche
            </h2>
            
            <form method="GET" class="space-y-4">
                <div class="grid md:grid-cols-4 gap-4">
                    <!-- Recherche -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            Rechercher
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="<?= e($search) ?>"
                                placeholder="Titre ou ID vidéo..."
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Format -->
                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                            Format
                        </label>
                        <select
                            id="format"
                            name="format"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">Tous les formats</option>
                            <option value="audio" <?= $format === 'audio' ? 'selected' : '' ?>>Audio seulement</option>
                            <option value="video" <?= $format === 'video' ? 'selected' : '' ?>>Vidéo seulement</option>
                        </select>
                    </div>

                    <!-- Date de début -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                            Du
                        </label>
                        <input
                            type="date"
                            id="date_from"
                            name="date_from"
                            value="<?= e($dateFrom) ?>"
                            max="<?= date('Y-m-d') ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Date de fin -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                            Au
                        </label>
                        <input
                            type="date"
                            id="date_to"
                            name="date_to"
                            value="<?= e($dateTo) ?>"
                            max="<?= date('Y-m-d') ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                    >
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                    <a 
                        href="history.php"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <i class="fas fa-times mr-2"></i>Effacer
                    </a>
                    <button 
                        type="button"
                        onclick="exportHistory()"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    >
                        <i class="fas fa-download mr-2"></i>Exporter CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Historique des téléchargements -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-purple-600"></i>
                        Historique 
                        <?php if ($page > 1 || $totalPages > 1): ?>
                            (Page <?= $page ?> sur <?= $totalPages ?>)
                        <?php endif; ?>
                    </h2>
                    <?php if ($totalRecords > 0): ?>
                        <span class="text-sm text-gray-500">
                            Affichage de <?= ($offset + 1) ?> à <?= min($offset + $perPage, $totalRecords) ?> 
                            sur <?= number_format($totalRecords) ?> résultats
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($downloads)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-500 mb-2">Aucun téléchargement trouvé</h3>
                    <?php if ($search || $format || $dateFrom || $dateTo): ?>
                        <p class="text-gray-400">Essayez de modifier vos filtres de recherche</p>
                        <a href="history.php" class="inline-block mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Voir tout l'historique
                        </a>
                    <?php else: ?>
                        <p class="text-gray-400">Vos téléchargements apparaîtront ici</p>
                        <a href="index.php" class="inline-block mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Commencer à télécharger
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vidéo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Format
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Taille
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($downloads as $download): ?>
                                <tr class="history-row">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-<?= $download['format'] === 'audio' ? 'file-audio' : 'file-video' ?> text-<?= $download['format'] === 'audio' ? 'green' : 'blue' ?>-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4 min-w-0">
                                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                                    <?= e($download['video_title'] ?: 'Titre indisponible') ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    ID: <?= e($download['video_id']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $download['format'] === 'audio' ? 'green' : 'blue' ?>-100 text-<?= $download['format'] === 'audio' ? 'green' : 'blue' ?>-800">
                                                <?= ucfirst($download['format']) ?>
                                            </span>
                                            <?php if ($download['quality']): ?>
                                                <span class="ml-2 text-xs text-gray-500">
                                                    <?= e($download['quality']) ?>p
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span><?= $download['formatted_date'] ?></span>
                                            <span class="text-xs text-gray-500">
                                                <?= timeAgo($download['download_date']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($download['file_size']): ?>
                                            <?= formatFileSize($download['file_size']) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <a 
                                                href="https://youtube.com/watch?v=<?= e($download['video_id']) ?>" 
                                                target="_blank"
                                                class="text-red-600 hover:text-red-800 transition-colors"
                                                title="Voir sur YouTube"
                                            >
                                                <i class="fab fa-youtube"></i>
                                            </a>
                                            <button
                                                onclick="redownload('<?= e($download['video_id']) ?>', '<?= $download['format'] ?>', '<?= $download['quality'] ?>')"
                                                class="text-purple-600 hover:text-purple-800 transition-colors"
                                                title="Télécharger à nouveau"
                                            >
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Affichage de <span class="font-medium"><?= ($offset + 1) ?></span> à 
                                <span class="font-medium"><?= min($offset + $perPage, $totalRecords) ?></span> 
                                sur <span class="font-medium"><?= number_format($totalRecords) ?></span> résultats
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <?php
                                $queryParams = $_GET;
                                unset($queryParams['page']);
                                unset($queryParams['export']); // Supprimer aussi le paramètre export des liens de pagination
                                $baseQuery = http_build_query($queryParams);
                                $baseUrl = 'history.php' . ($baseQuery ? '?' . $baseQuery . '&' : '?');
                                ?>

                                <!-- Bouton Précédent -->
                                <?php if ($page > 1): ?>
                                    <a 
                                        href="<?= $baseUrl ?>page=<?= ($page - 1) ?>"
                                        class="pagination-btn relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="pagination-btn disabled relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                <?php endif; ?>

                                <!-- Numéros de page -->
                                <?php
                                $start = max(1, $page - 2);
                                $end = min($totalPages, $page + 2);
                                
                                if ($start > 1): ?>
                                    <a href="<?= $baseUrl ?>page=1" class="pagination-btn relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                                    <?php if ($start > 2): ?>
                                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <?php if ($i == $page): ?>
                                        <span class="pagination-btn relative inline-flex items-center px-3 py-2 rounded-md border border-purple-500 bg-purple-600 text-sm font-medium text-white"><?= $i ?></span>
                                    <?php else: ?>
                                        <a href="<?= $baseUrl ?>page=<?= $i ?>" class="pagination-btn relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><?= $i ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($end < $totalPages): ?>
                                    <?php if ($end < $totalPages - 1): ?>
                                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500">...</span>
                                    <?php endif; ?>
                                    <a href="<?= $baseUrl ?>page=<?= $totalPages ?>" class="pagination-btn relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><?= $totalPages ?></a>
                                <?php endif; ?>

                                <!-- Bouton Suivant -->
                                <?php if ($page < $totalPages): ?>
                                    <a 
                                        href="<?= $baseUrl ?>page=<?= ($page + 1) ?>"
                                        class="pagination-btn relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                    >
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="pagination-btn disabled relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2 text-purple-600"></i>Actions rapides
            </h2>
            <div class="grid md:grid-cols-2 gap-4">
                <button 
                    onclick="clearFilters()" 
                    class="flex items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    <i class="fas fa-eraser text-gray-600 text-xl mr-3"></i>
                    <div class="text-left">
                        <div class="font-medium text-gray-800">Effacer les filtres</div>
                        <div class="text-sm text-gray-600">Voir tout l'historique</div>
                    </div>
                </button>
                
                <button 
                    onclick="exportHistory()" 
                    class="flex items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
                >
                    <i class="fas fa-file-export text-green-600 text-xl mr-3"></i>
                    <div class="text-left">
                        <div class="font-medium text-green-800">Exporter en CSV</div>
                        <div class="text-sm text-green-600">Télécharger l'historique</div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Retour au dashboard -->
        <div class="mt-8 text-center">
            <a 
                href="dashboard.php" 
                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium"
            >
                <i class="fas fa-tachometer-alt mr-2"></i>
                Retour au Dashboard
            </a>
        </div>
    </div>

    <script>
        // Menu mobile toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Fonction pour effacer les filtres
        function clearFilters() {
            window.location.href = 'history.php';
        }

        // Fonction pour exporter l'historique
        function exportHistory() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('export', 'csv');
            
            // Rediriger vers la même page avec le paramètre export
            window.location.href = currentUrl.toString();
        }

        // Fonction pour retélécharger
        function redownload(videoId, format, quality) {
            const url = `index.php?video_id=${videoId}&format=${format}&quality=${quality}`;
            window.open(url, '_blank');
            showNotification('Redirection vers la page de téléchargement...', 'info');
        }

        // Fonction pour afficher les notifications
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 px-4 py-3 rounded-lg shadow-lg z-50 text-white transition-all duration-300 transform translate-x-full`;
            
            switch(type) {
                case 'success':
                    notification.classList.add('bg-green-600');
                    break;
                case 'error':
                    notification.classList.add('bg-red-600');
                    break;
                case 'info':
                    notification.classList.add('bg-blue-600');
                    break;
                default:
                    notification.classList.add('bg-gray-600');
            }
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Animation de sortie et suppression
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Auto-soumission du formulaire lors du changement des dates
        document.getElementById('date_from').addEventListener('change', function() {
            if (this.value && document.getElementById('date_to').value) {
                this.form.submit();
            }
        });
        
        document.getElementById('date_to').addEventListener('change', function() {
            if (this.value && document.getElementById('date_from').value) {
                this.form.submit();
            }
        });

        // Validation des dates
        document.getElementById('date_from').addEventListener('change', function() {
            const dateTo = document.getElementById('date_to');
            if (this.value) {
                dateTo.min = this.value;
            }
        });

        document.getElementById('date_to').addEventListener('change', function() {
            const dateFrom = document.getElementById('date_from');
            if (this.value) {
                dateFrom.max = this.value;
            }
        });

        // Recherche en temps réel (optionnel - avec délai)
        let searchTimeout;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();
            
            if (searchTerm.length > 2 || searchTerm.length === 0) {
                searchTimeout = setTimeout(() => {
                    // Auto-submit si plus de 2 caractères ou vide pour effacer
                    this.form.submit();
                }, 500);
            }
        });
    </script>
</body>
</html>