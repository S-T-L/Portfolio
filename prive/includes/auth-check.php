<?php
require_once __DIR__ . '/session.php';

prive_demarrer_session();

$redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');

if (empty($_SESSION['prive_auth'])) {
    header('Location: /prive/login.php?redirect=' . $redirect);
    exit;
}

$maintenant = time();

if ($maintenant - ($_SESSION['derniere_activite'] ?? 0) > SESSION_INACTIVITE
    || $maintenant - ($_SESSION['connexion'] ?? 0) > SESSION_DUREE_MAX) {
    prive_journal('EXPIRATION');
    prive_fermer_session();
    header('Location: /prive/login.php?expire=1&redirect=' . $redirect);
    exit;
}

if ($maintenant - ($_SESSION['renouvellement'] ?? 0) > SESSION_RENOUVELLEMENT) {
    session_regenerate_id(true);
    $_SESSION['renouvellement'] = $maintenant;
}

$_SESSION['derniere_activite'] = $maintenant;
