<?php
// Formulaire de contact : vérifie le message puis l'envoie sur la boîte Gmail du portfolio (SMTP via PHPMailer).
// Réponse JSON pour app.js ; sans JavaScript, redirection vers la section Contact de l'accueil.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/prive/includes/session.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Au plus 3 messages par heure et par visiteur, 20 par jour pour l'ensemble du site
const ENVOIS_MAX_VISITEUR = 3;
const ENVOIS_PERIODE_VISITEUR = 3600;
const ENVOIS_MAX_JOUR = 20;
const ERREUR_ENVOI = "L'envoi a échoué, réessayez plus tard ou écrivez-moi sur LinkedIn.";

$json = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

function repondre(bool $ok, string $message, int $code = 200): void
{
    global $json;
    if ($json) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
    } else {
        header('Location: index.html#' . ($ok ? 'contact-envoye' : 'contact-erreur'), true, 303);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html#contact');
    exit;
}

// Champ piège invisible : rempli, c'est un robot. On répond « envoyé » sans rien envoyer.
if (($_POST['site'] ?? '') !== '') {
    repondre(true, 'Merci, votre message a bien été envoyé.');
}

$nom = trim((string) ($_POST['nom'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

// Aucun retour à la ligne dans le nom : il est repris dans le sujet et l'en-tête « Répondre à »
if ($nom === '' || mb_strlen($nom) > 100 || preg_match('/[\r\n]/', $nom)) {
    repondre(false, 'Veuillez indiquer votre nom (100 caractères au plus).', 400);
}
if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    repondre(false, 'Veuillez indiquer une adresse e-mail valide.', 400);
}
if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) {
    repondre(false, 'Votre message doit contenir entre 10 et 5 000 caractères.', 400);
}

// Limite d'envois : seule une empreinte de l'adresse IP est conservée, effacée après une heure.
// Renvoie true si la limite est atteinte ; $enregistrer ajoute l'envoi courant (uniquement après un envoi réussi).
function limite_envois(string $visiteur, bool $enregistrer): bool
{
    $verrou = fopen(prive_dossier_data() . '/contact-envois.json', 'c+');
    flock($verrou, LOCK_EX);
    $envois = json_decode(stream_get_contents($verrou), true) ?: [];
    $maintenant = time();
    $envois = array_values(array_filter($envois, function ($envoi) use ($maintenant) {
        return $maintenant - $envoi['date'] < 86400;
    }));
    if ($enregistrer) {
        $envois[] = ['visiteur' => $visiteur, 'date' => $maintenant];
    }
    // Au-delà d'une heure, l'empreinte n'est plus utile : seule la date reste pour la limite journalière
    foreach ($envois as &$envoi) {
        if ($maintenant - $envoi['date'] >= ENVOIS_PERIODE_VISITEUR) {
            $envoi['visiteur'] = '';
        }
    }
    unset($envoi);
    $envoisVisiteur = array_filter($envois, function ($envoi) use ($visiteur) {
        return $envoi['visiteur'] === $visiteur;
    });
    ftruncate($verrou, 0);
    rewind($verrou);
    fwrite($verrou, json_encode($envois));
    flock($verrou, LOCK_UN);
    fclose($verrou);
    return count($envoisVisiteur) >= ENVOIS_MAX_VISITEUR || count($envois) >= ENVOIS_MAX_JOUR;
}

$visiteur = hash_hmac('sha256', $_SERVER['REMOTE_ADDR'] ?? '', PRIVE_PASSWORD_HASH);
if (limite_envois($visiteur, false)) {
    repondre(false, 'Trop de messages envoyés. Réessayez plus tard ou écrivez-moi sur LinkedIn.', 429);
}

if (!defined('CONTACT_GMAIL') || !defined('CONTACT_MOT_DE_PASSE')) {
    error_log('Formulaire de contact : CONTACT_GMAIL ou CONTACT_MOT_DE_PASSE absent de prive/config.php');
    repondre(false, ERREUR_ENVOI, 500);
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth = true;
    $mail->Username = CONTACT_GMAIL;
    // Google affiche le mot de passe d'application par groupes de 4 : les espaces sont retirés
    $mail->Password = str_replace(' ', '', CONTACT_MOT_DE_PASSE);
    $mail->Timeout = 15;
    $mail->CharSet = PHPMailer::CHARSET_UTF8;

    // L'expéditeur reste l'adresse du portfolio, le visiteur est en « Répondre à »
    $mail->setFrom(CONTACT_GMAIL, 'Portfolio - formulaire de contact');
    $mail->addAddress(CONTACT_GMAIL);
    $mail->addReplyTo($email, $nom);
    $mail->Subject = 'Portfolio : message de ' . $nom;
    $mail->Body = "Nom : $nom\nE-mail : $email\n\n$message";
    $mail->send();
} catch (Exception $e) {
    // Détail technique dans le journal du serveur uniquement, jamais affiché au visiteur
    error_log('Formulaire de contact : ' . $mail->ErrorInfo);
    repondre(false, ERREUR_ENVOI, 500);
}

limite_envois($visiteur, true);
repondre(true, 'Merci, votre message a bien été envoyé. Je vous réponds au plus vite.');
