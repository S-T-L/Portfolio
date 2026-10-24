<?php
require_once __DIR__ . '/includes/session.php';

prive_demarrer_session();
if (!empty($_SESSION['prive_auth'])) {
    prive_journal('DECONNEXION');
}
prive_fermer_session();

header('Location: /prive/login.php');
exit;
