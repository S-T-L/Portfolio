<?php
// Gestion de la session et du journal.
require_once __DIR__ . '/../config.php';

date_default_timezone_set('Pacific/Noumea');

// Durées en secondes : 30 min, 2 h, 15 min, 180 jours
const SESSION_INACTIVITE = 1800;
const SESSION_DUREE_MAX = 7200;
const SESSION_RENOUVELLEMENT = 900;
const JOURNAL_CONSERVATION = 15552000;

function prive_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? '') == 443
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function prive_dossier_data(): string
{
    $dossier = defined('PRIVE_DATA_DIR') ? PRIVE_DATA_DIR : dirname(__DIR__, 2) . '/prive-data';
    if (!is_dir($dossier)) {
        mkdir($dossier, 0700, true);
    }
    if (!is_file($dossier . '/.htaccess')) {
        file_put_contents($dossier . '/.htaccess', "Require all denied\n");
    }
    return $dossier;
}

function prive_demarrer_session(): void
{
    $https = prive_https();
    $sessions = prive_dossier_data() . '/sessions';
    if (!is_dir($sessions)) {
        mkdir($sessions, 0700, true);
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.save_path', $sessions);
    ini_set('session.gc_maxlifetime', (string) SESSION_DUREE_MAX);
    ini_set('session.gc_probability', '1');
    ini_set('session.gc_divisor', '100');

    session_name(($https ? '__Host-' : '') . PRIVE_SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function prive_fermer_session(): void
{
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $params['path'],
        'secure'   => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'],
    ]);
    session_destroy();
}

function prive_journal(string $evenement): void
{
    $fichier = prive_dossier_data() . '/connexions.log';
    $limite = date('Y-m-d H:i:s', time() - JOURNAL_CONSERVATION);
    $lignes = is_file($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $lignes = array_filter($lignes, function ($ligne) use ($limite) {
        return substr($ligne, 0, 19) >= $limite;
    });
    $lignes[] = date('Y-m-d H:i:s') . ' | ' . $evenement;
    file_put_contents($fichier, implode(PHP_EOL, $lignes) . PHP_EOL, LOCK_EX);
}
