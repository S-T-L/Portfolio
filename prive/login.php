<?php
require_once __DIR__ . '/includes/session.php';

// Après chaque échec, attente de 1, 2, 4, 8… secondes (30 s au plus), remise à zéro après 15 min sans échec.
const PAUSE_MAX = 30;
const PAUSE_REMISE_A_ZERO = 900;

prive_demarrer_session();

$redirect = $_GET['redirect'] ?? '/prive/index.php';
if (strpos($redirect, '/prive/') !== 0) {
    $redirect = '/prive/index.php';
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$error = isset($_GET['expire']) ? 'Votre session a expiré, veuillez vous reconnecter.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verrou = fopen(prive_dossier_data() . '/tentatives.json', 'c+');
    flock($verrou, LOCK_EX);
    $etat = json_decode(stream_get_contents($verrou), true) ?: ['echecs' => 0, 'dernier_echec' => 0, 'prochain_essai' => 0];
    $maintenant = time();
    if ($maintenant - $etat['dernier_echec'] > PAUSE_REMISE_A_ZERO) {
        $etat['echecs'] = 0;
    }

    if ($maintenant < $etat['prochain_essai']) {
        $attente = $etat['prochain_essai'] - $maintenant;
        $error = 'Trop de tentatives. Veuillez patienter ' . $attente . ' seconde' . ($attente > 1 ? 's' : '') . ' avant de réessayer.';
    } elseif (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $error = 'La page a expiré, veuillez réessayer.';
    } elseif (password_verify($_POST['password'] ?? '', PRIVE_PASSWORD_HASH)) {
        $etat = ['echecs' => 0, 'dernier_echec' => 0, 'prochain_essai' => 0];
        session_regenerate_id(true);
        $_SESSION = [
            'prive_auth'        => true,
            'connexion'         => $maintenant,
            'derniere_activite' => $maintenant,
            'renouvellement'    => $maintenant,
        ];
        prive_journal('SUCCES');
    } else {
        $etat['echecs']++;
        $etat['dernier_echec'] = $maintenant;
        $etat['prochain_essai'] = $maintenant + min(2 ** ($etat['echecs'] - 1), PAUSE_MAX);
        prive_journal('ECHEC');
        $error = 'Mot de passe incorrect.';
    }

    ftruncate($verrou, 0);
    rewind($verrou);
    fwrite($verrou, json_encode($etat));
    flock($verrou, LOCK_UN);
    fclose($verrou);

    if (!empty($_SESSION['prive_auth'])) {
        header('Location: ' . $redirect);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Espace enseignants — Estelle · Développeuse web</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="icon" type="image/svg+xml" href="../assets/favicon.svg">
</head>

<body>

  <nav class="menu" aria-label="Navigation principale">
    <div class="menu__corps">
      <a class="marque" href="../index.html"><img src="../assets/logo-ef.svg" alt="Estelle, développeuse web : retour à l'accueil"></a>
      <button class="menu__bouton" type="button" aria-label="Menu" aria-expanded="false" aria-controls="menu-liens">≡</button>
      <div class="menu__liens" id="menu-liens">
        <a href="../index.html">Accueil</a>
        <a href="login.php" aria-current="true">Espace enseignants</a>
      </div>
      <div class="menu__pied">
        <a href="https://www.linkedin.com/in/estelle-f-b1318629b" target="_blank" rel="noopener">LinkedIn</a>
        <a href="https://github.com/S-T-L" target="_blank" rel="noopener">GitHub</a>
        <small>© 2026 Estelle</small>
      </div>
    </div>
  </nav>

  <main>

    <header class="intro-page">
      <div class="grain" aria-hidden="true"></div>
      <div class="interieur">
        <p class="etiquette">Espace enseignants</p>
        <h1>Espace privé</h1>
      </div>
    </header>

    <section class="bloc bloc--clair">
      <div class="interieur">
        <div class="carte connexion">
          <span class="etiquette">Connexion</span>
          <p>Cet espace est réservé aux enseignants.</p>
          <form class="formulaire formulaire--simple" method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <label for="password">Mot de passe *<input type="password" id="password" name="password" required autofocus></label>
            <?php if ($error): ?>
              <p class="erreur"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <button type="submit">Se connecter</button>
          </form>
        </div>
      </div>
    </section>

  </main>

  <footer class="pied">
    <span>Espace privé — Estelle, 2026</span>
    <a href="../index.html">Retour au portfolio</a>
  </footer>

  <script src="../app.js"></script>
</body>

</html>
