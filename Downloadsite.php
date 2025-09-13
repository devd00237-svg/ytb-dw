<?php
/**
 * Script pour compresser tout le site en un fichier ZIP
 */

$rootPath = __DIR__; // dossier de ton site
$zipFile = __DIR__ . '/ytb-dw.zip'; // fichier zip final

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    exit("❌ Impossible de créer le fichier ZIP\n");
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($files as $file) {
    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($rootPath) + 1);

    if ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
    } else {
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();

// Redirection vers le fichier ZIP pour téléchargement direct
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
header('Content-Length: ' . filesize($zipFile));
readfile($zipFile);
exit;
