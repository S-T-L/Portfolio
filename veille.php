<?php
// Relais des flux RSS des collections Raindrop « IA » et « Protection des données » vers le portfolio (section Veille).
// Le résultat est mis en cache 15 min pour ne pas interroger Raindrop à chaque visite.

const FLUX = [
    'https://developer-tools-mail.raindrop.page/ia-75626378/feed',
    'https://developer-tools-mail.raindrop.page/protection-des-donnees-75864770/feed',
];
const NB_ARTICLES = 5;
const DUREE_CACHE = 900;

header('Content-Type: application/json; charset=utf-8');

$cache = sys_get_temp_dir() . '/portfolio-veille.json';
if (is_file($cache) && time() - filemtime($cache) < DUREE_CACHE) {
    readfile($cache);
    exit;
}

$contexte = stream_context_create(['http' => ['timeout' => 5, 'user_agent' => 'Portfolio Estelle']]);
$items = [];
$fluxLus = 0;
foreach (FLUX as $flux) {
    // paramètre unique pour contourner le cache d'1 h des serveurs de Raindrop
    $xml = @file_get_contents($flux . '?t=' . time(), false, $contexte);
    $rss = $xml ? @simplexml_load_string($xml) : false;
    if (!$rss) continue;
    $fluxLus++;
    foreach ($rss->channel->item as $item) {
        $items[] = $item;
    }
}

if (!$fluxLus) {
    // Raindrop injoignable : on ressert l'ancien cache s'il existe, sinon une liste vide
    echo is_file($cache) ? file_get_contents($cache) : '[]';
    exit;
}

usort($items, function ($a, $b) {
    return strtotime((string) $b->pubDate) - strtotime((string) $a->pubDate);
});

$mois = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
$articles = [];
foreach ($items as $item) {
    $lien = (string) $item->link;
    if (!preg_match('#^https?://#', $lien)) continue;
    $horodatage = strtotime((string) $item->pubDate);
    $articles[] = [
        'titre'  => trim((string) $item->title),
        'lien'   => $lien,
        'source' => preg_replace('/^www\./', '', parse_url($lien, PHP_URL_HOST)),
        'date'   => $horodatage ? date('j', $horodatage) . ' ' . $mois[date('n', $horodatage) - 1] . ' ' . date('Y', $horodatage) : '',
    ];
    if (count($articles) >= NB_ARTICLES) break;
}

$json = json_encode($articles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@file_put_contents($cache, $json);
echo $json;
