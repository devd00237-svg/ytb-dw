 <?php
/**
 * Script PHP pour parcourir un dossier et remplacer un élément
 * 
 * - Si aucun dossier n’est précisé → prend __DIR__
 * - Si un dossier est précisé → n’agit que sur ce dossier
 */

// 🔹 Paramètre optionnel : dossier cible
$targetDir = $argv[1] ?? '';   // si lancé en CLI : php script.php monDossier
if (!empty($_GET['dir'])) {    // si lancé via navigateur : script.php?dir=monDossier
    $targetDir = $_GET['dir'];
}

// 🔹 Choix du dossier de départ
if ($targetDir !== '') {
    $directory = realpath(__DIR__ . DIRECTORY_SEPARATOR . $targetDir);
    if (!$directory || !is_dir($directory)) {
        die("❌ Erreur : Le dossier '$targetDir' n’existe pas.\n");
    }
} else {
    $directory = __DIR__; // par défaut : dossier courant
}

// 🔹 Texte à rechercher et remplacer
$search = 'https://images.unsplash.com/photo-1494790108755-2616b612b829?w=300&h=300&fit=crop&crop=face';

$replace = 'https://images.unsplash.com/photo-1743346268290-5c5ac58ea2ba?q=80&w=686&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D
';

// 🔹 Extensions ciblées (évite de casser des binaires)
$extensions = ['html'];

// 🔹 Itération récursive sur le dossier choisi
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

foreach ($rii as $file) {
    if ($file->isDir()) continue;

    $filePath = $file->getPathname();
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);

    if (in_array($ext, $extensions)) {
        $content = file_get_contents($filePath);

        if (strpos($content, $search) !== false) {
            $newContent = str_replace($search, $replace, $content);
            file_put_contents($filePath, $newContent);
            echo "✅ Modifié : $filePath\n";
        }
    }
}