<?php
// Envoi d'une fiche réflective : les PDF sont rangés hors du site, dans prive-data/fiches.
require_once __DIR__ . '/includes/auth-check.php';

// Liste fixe : seules ces fiches peuvent être demandées.
const FICHES = [
    'tenjity'       => 'tenjity.pdf',
    'safe-campus-1' => 'safe-campus-1.pdf',
    'safe-campus-2' => 'safe-campus-2.pdf',
    'safe-campus-3' => 'safe-campus-3.pdf',
];

$cle = $_GET['f'] ?? '';
$chemin = isset(FICHES[$cle]) ? prive_dossier_data() . '/fiches/' . FICHES[$cle] : '';

if ($chemin === '' || !is_file($chemin)) {
    http_response_code(404);
    exit('Fiche introuvable.');
}

// Libère la session pour ne pas bloquer les autres pages pendant l'envoi.
session_write_close();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="fiche-reflective-' . $cle . '.pdf"');
header('Content-Length: ' . filesize($chemin));
header('Cache-Control: private, no-store');
header('X-Robots-Tag: noindex, nofollow');
readfile($chemin);
