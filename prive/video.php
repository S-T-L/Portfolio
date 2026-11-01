<?php
require_once __DIR__ . '/includes/auth-check.php';

const VIDEOS = [
    'cv' => 'cv-video.mp4',
];

$cle = $_GET['v'] ?? '';
$chemin = isset(VIDEOS[$cle]) ? prive_dossier_data() . '/videos/' . VIDEOS[$cle] : '';

if ($chemin === '' || !is_file($chemin)) {
    http_response_code(404);
    exit('Vidéo introuvable.');
}

// Libère la session pour ne pas bloquer les autres pages pendant l'envoi.
session_write_close();

$taille = filesize($chemin);
$debut = 0;
$fin = $taille - 1;

// Le navigateur demande la vidéo par morceaux (en-tête Range) pour pouvoir avancer dedans.
if (isset($_SERVER['HTTP_RANGE'])) {
    if (!preg_match('/^bytes=(\d*)-(\d*)$/', $_SERVER['HTTP_RANGE'], $m) || ($m[1] === '' && $m[2] === '')) {
        http_response_code(416);
        header('Content-Range: bytes */' . $taille);
        exit;
    }
    if ($m[1] === '') {
        $debut = max(0, $taille - (int) $m[2]);
    } else {
        $debut = (int) $m[1];
        if ($m[2] !== '') {
            $fin = min((int) $m[2], $taille - 1);
        }
    }
    if ($debut > $fin) {
        http_response_code(416);
        header('Content-Range: bytes */' . $taille);
        exit;
    }
    http_response_code(206);
    header('Content-Range: bytes ' . $debut . '-' . $fin . '/' . $taille);
}

header('Content-Type: video/mp4');
header('Accept-Ranges: bytes');
header('Content-Length: ' . ($fin - $debut + 1));
header('Cache-Control: private, no-store');
header('X-Robots-Tag: noindex, nofollow');

set_time_limit(0);
$flux = fopen($chemin, 'rb');
fseek($flux, $debut);
$reste = $fin - $debut + 1;
while ($reste > 0 && !feof($flux) && !connection_aborted()) {
    $morceau = fread($flux, min(65536, $reste));
    echo $morceau;
    flush();
    $reste -= strlen($morceau);
}
fclose($flux);
