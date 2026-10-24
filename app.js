(function () {
  // Menu mobile
  var menu = document.querySelector('.menu'), bouton = document.querySelector('.menu__bouton');
  if (!menu || !bouton) return;
  function ouvrir(etat) { menu.classList.toggle('ouvert', etat); bouton.setAttribute('aria-expanded', etat ? 'true' : 'false'); }
  bouton.addEventListener('click', function () { ouvrir(!menu.classList.contains('ouvert')); });
  var liens = menu.querySelectorAll('.menu__liens a');
  liens.forEach(function (a) { a.addEventListener('click', function () { ouvrir(false); }); });

  // Lien du menu actif selon la section visible (page d'accueil uniquement)
  var ancres = Array.prototype.filter.call(liens, function (a) { return a.getAttribute('href').charAt(0) === '#'; });
  if (ancres.length && 'IntersectionObserver' in window) {
    var suivi = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (e) {
        if (!e.isIntersecting) return;
        ancres.forEach(function (a) { a.setAttribute('aria-current', a.getAttribute('href') === '#' + e.target.id ? 'true' : 'false'); });
      });
    }, { rootMargin: '-45% 0px -50% 0px' });
    ancres.forEach(function (a) { var cible = document.querySelector(a.getAttribute('href')); if (cible) suivi.observe(cible); });
  }

  // Apparition au scroll
  var blocs = document.querySelectorAll('.apparait');
  if ('IntersectionObserver' in window) {
    var vue = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('vu'); vue.unobserve(e.target); } });
    }, { rootMargin: '0px 0px -8% 0px' });
    blocs.forEach(function (el) { vue.observe(el); });
  } else {
    blocs.forEach(function (el) { el.classList.add('vu'); });
  }

  // Dernières lectures de veille (flux Raindrop relayé par veille.php) — le bloc reste masqué en cas d'échec
  var lectures = document.getElementById('lectures');
  if (lectures && window.fetch) {
    fetch('veille.php')
      .then(function (r) { return r.ok ? r.json() : []; })
      .then(function (articles) {
        if (!articles.length) return;
        var liste = lectures.querySelector('.lectures__liste');
        articles.forEach(function (art) {
          // date « 7 oct. 2026 » affichée en grand : jour sur deux chiffres, mois en dessous
          var morceaux = art.date.split(' ');
          var li = document.createElement('li'), a = document.createElement('a');
          var date = document.createElement('span'), jour = document.createElement('b'), mois = document.createElement('span');
          var texte = document.createElement('span'), titre = document.createElement('strong'), source = document.createElement('small');
          a.href = art.lien; a.target = '_blank'; a.rel = 'noopener';
          date.className = 'lectures__date';
          jour.textContent = morceaux[0] ? ('0' + morceaux[0]).slice(-2) : '';
          mois.textContent = morceaux[1] || '';
          titre.textContent = art.titre;
          source.textContent = art.source;
          date.appendChild(jour); date.appendChild(mois);
          texte.appendChild(titre); texte.appendChild(source);
          a.appendChild(date); a.appendChild(texte); li.appendChild(a); liste.appendChild(li);
        });
        lectures.hidden = false;
      })
      .catch(function () {});
  }

  // Vidéos YouTube chargées au clic (consentement) — sans JS, le lien ouvre la vidéo sur YouTube
  document.querySelectorAll('.video__lancer').forEach(function (lien) {
    lien.addEventListener('click', function (e) {
      e.preventDefault();
      var lecteur = document.createElement('iframe');
      lecteur.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(lien.dataset.video) + '?autoplay=1';
      lecteur.title = lien.dataset.titre;
      lecteur.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
      lecteur.allowFullscreen = true;
      lien.replaceWith(lecteur);
    });
  });
})();
