<?php
// Modèle de configuration : copier en « config.php » et renseigner les valeurs.

// Hash du mot de passe, à générer avec :
//   php -r "echo password_hash(readline('Mot de passe : '), PASSWORD_DEFAULT), PHP_EOL;"
define('PRIVE_PASSWORD_HASH', 'remplacer-par-le-hash-du-mot-de-passe');

define('PRIVE_SESSION_NAME', 'portfolio_prive');

// Formulaire de contact : compte Gmail du portfolio et son mot de passe d'application
// (Compte Google > Sécurité > Mots de passe des applications, 16 caractères)
define('CONTACT_GMAIL', 'adresse-du-portfolio@gmail.com');
define('CONTACT_MOT_DE_PASSE', 'remplacer-par-le-mot-de-passe-d-application');

// Chemin absolu du dossier de données (facultatif en local)
// define('PRIVE_DATA_DIR', '/chemin/absolu/vers/le/dossier');
