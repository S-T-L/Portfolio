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
      <a class="marque" href="../index.html"><img src="../assets/logo-ef.svg" alt="Estelle, développeuse web : retour à l'accueil"></a>
      <button class="menu__bouton" type="button" aria-label="Menu" aria-expanded="false" aria-controls="menu-liens">≡</button>
      <div class="menu__liens" id="menu-liens">
        <a href="../index.html">Accueil</a>
        <a href="index.php" aria-current="true">Fiches réflectives</a>
        <a href="#cv-video">CV vidéo</a>
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
        <div class="fiches fiches--projets">

          <article class="carte fiche-projet">
            <div class="fiche-projet__tete">
              <span class="badge">Alternance</span>
              <span class="fiche-projet__compte">1 fiche</span>
            </div>
            <h3>Module d'accessibilité Tenjity</h3>
            <p class="fiche-projet__sous">Accessibilité web embarquée</p>
            <p>Ajout de fonctionnalités, refactorisation et intégration d'un module d'accessibilité web embarqué sur des sites tiers, développé en Vue.js 3.</p>
            <div class="puces"><span>Vue.js 3</span><span>JavaScript</span><span>SCSS</span><span>Nuxt 3</span></div>
            <span class="etiquette fiche-projet__titre">Fiches réflectives</span>
            <div class="docs docs--fiches">
              <a href="fiche.php?f=tenjity" target="_blank" rel="noopener">Fiche réflective</a>
            </div>
            <div class="fiche-projet__liens">
              <a href="../projet-tenjity.html">Voir le projet</a>
            </div>
          </article>

          <article class="carte fiche-projet">
            <div class="fiche-projet__tete">
              <span class="badge">SAE</span>
              <span class="fiche-projet__compte">3 fiches</span>
            </div>
            <h3>Safe Campus</h3>
            <p class="fiche-projet__sous">Front Nuxt et back Laravel</p>
            <p>Plateforme web de prévention et d'accompagnement des conduites à risques en milieu universitaire : santé mentale, conduites addictives, violences sexistes et sexuelles.</p>
            <div class="puces"><span>Nuxt 4</span><span>Laravel</span><span>Filament</span><span>PostgreSQL</span><span>Docker</span></div>
            <span class="etiquette fiche-projet__titre">Fiches réflectives</span>
            <div class="docs docs--fiches">
              <a href="fiche.php?f=safe-campus-1" target="_blank" rel="noopener">Fiche réflective 1</a>
              <a href="fiche.php?f=safe-campus-2" target="_blank" rel="noopener">Fiche réflective 2</a>
              <a href="fiche.php?f=safe-campus-3" target="_blank" rel="noopener">Fiche réflective 3</a>
            </div>
            <div class="fiche-projet__liens">
              <a href="../projet-safe-campus.html">Voir le projet</a>
              <a href="https://github.com/Korrighed/safe-campus-front" target="_blank" rel="noopener">GitHub front</a>
              <a href="https://github.com/Korrighed/safe-campus-back" target="_blank" rel="noopener">GitHub back</a>
            </div>
          </article>

        </div>
      </div>
    </section>

    <section class="bloc bloc--sombre" id="cv-video">
      <div class="interieur">
        <div class="entete">
          <h2 class="entete__titre">CV vidéo</h2>
        </div>
        <video class="video-cv" controls preload="metadata">
          <source src="video.php?v=cv" type="video/mp4">
          Votre navigateur ne peut pas lire cette vidéo.
        </video>
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
