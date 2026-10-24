<?php require_once __DIR__ . '/includes/auth-check.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Espace enseignants — Estelle · Développeuse web</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 64 64%27%3E%3Crect width=%2764%27 height=%2764%27 rx=%2714%27 fill=%27%230b0a09%27/%3E%3Ctext x=%2732%27 y=%2747%27 font-family=%27Impact,sans-serif%27 font-size=%2740%27 fill=%27%23a57865%27 text-anchor=%27middle%27%3EE%3C/text%3E%3C/svg%3E">
</head>

<body>

  <nav class="menu" aria-label="Navigation principale">
    <div class="menu__corps">
      <a class="marque" href="../index.html"><span class="marque__nom">ESTELLE</span><span class="marque__ext">.DEV</span></a>
      <button class="menu__bouton" type="button" aria-label="Menu" aria-expanded="false" aria-controls="menu-liens">≡</button>
      <div class="menu__liens" id="menu-liens">
        <a href="../index.html">Accueil</a>
        <a href="index.php" aria-current="true">Fiches réflectives</a>
        <a href="logout.php">Se déconnecter</a>
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
        <h1>Fiches réflectives</h1>
      </div>
    </header>

    <section class="bloc bloc--clair">
      <div class="interieur">
        <p class="texte-intro">Ces fiches font le lien entre les projets présentés dans ce portfolio et les compétences du référentiel BTS SIO.</p>
        <div class="fiches">
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
