<?php
// File: templates/element/botomenuppal.php

use Cake\Routing\Router;

$text = $text ?? '';
$image = $image ?? 'default.png';
$link = $link ?? '#';
$target = $target ?? null;
$title = $title ?? '';
$menu = $menu ?? [];
$color = $color ?? 'gris'; // Valor per defecte
$onclick = $onclick ?? null;
$onclickAttr = $onclick ? ' onclick="' . h($onclick) . '"' : '';
$confirm = $confirm ?? null;
$confirmId = 'confirm-box-' . uniqid();


$colorMap = [
    'rosa' => '#e55381',
    'blaucel' => '#8ec3c3',
    'lila' => '#b2abbe',
    'taronja' => '#feb20e',
    'blaumari' => '#708090',
    'verd' => '#aed581',
    'gris' => '#bfbfbf',
    'ocre' => '#d8baa9',
    'grisclar' => '#cfcfcf',
    'negre' => '#000000'
];

$bgColorCode = $colorMap[$color] ?? '#f0f0f0';

if (!function_exists('lightColor')) {
    function lightColor($hex, $percent = 20) {
        $hex = str_replace('#', '', $hex);
        $r = min(255, intval(hexdec(substr($hex, 0, 2)) * (100 + $percent) / 100));
        $g = min(255, intval(hexdec(substr($hex, 2, 2)) * (100 + $percent) / 100));
        $b = min(255, intval(hexdec(substr($hex, 4, 2)) * (100 + $percent) / 100));
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}

$hoverColor = lightColor($bgColorCode, 20);


// Determina URL
if (is_array($link)) {
    $url = $this->Url->build($link);
} else {
    $url = $link;
}

$targetAttr = $target === '_blank' ? ' target="_blank"' : '';
$titleAttr = $title ? ' title="' . h($title) . '"' : '';

$menuId = 'menu_' . uniqid();
?>

<div class="botomenuppal-wrapper">

<a href="<?= h($url) ?>"<?= $targetAttr . $titleAttr . $onclickAttr ?>
   class="botomenuppal" onclick="event.stopPropagation();"
   style="--accent-color: <?= h($bgColorCode) ?>;">
    <div class="botomenuppal-inner">
        <div class="botomenuppal-img">
            <?= $this->Html->image($image, ['alt' => 'icon']) ?>
        </div>
        <div class="botomenuppal-text">
            <span><?= h($text) ?></span>
        </div>
    </div>
</a>


</div>

<style>
.botomenuppal {
    text-decoration: none !important;
    color: inherit; /* opcional: hereta el color del context */
}
.botomenuppal:link,
.botomenuppal:visited,
.botomenuppal:hover,
.botomenuppal:active {
    text-decoration: none !important;
}

.botomenuppal-inner {
    display: flex;
    align-items: center;
    min-height: 3rem;
    min-width: 7rem;
    overflow: hidden;            /* important per a l’animació */
}

/* ICONA: fons sempre del color accent */
.botomenuppal-img {
    flex: 0 0 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--accent-color, #666);
}
.botomenuppal-img img {
    max-width: 70%;
    max-height: 70%;
    object-fit: contain;
}

/* TEXT amb overlay animat */
.botomenuppal-text {
    display: flex;
    height: 3rem;
    position: relative;
    flex: 1;
    text-align: left;
    align-items: center;
    padding-left: 0.5rem;
    text-transform: uppercase;
    background: #fff;
    line-height: 1.2;
    font-weight: bold;
    font-size: 0.7rem;
    color: black;                /* color base */
    overflow: hidden;            /* amaga l’overlay mentre entra */
}

/* L’overlay de color que entra d’esquerra a dreta */
.botomenuppal-text::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;           /* top:0; right:auto; bottom:0; left:0 */
    width: 0%;
    background: var(--accent-color, #666);
    transition: width .35s ease; /* velocitat de l’animació */
    z-index: 0;
}

/* En hover: omple tot el fons del text */
.botomenuppal:hover .botomenuppal-text::before {
    width: 100%;
}

/* Assegura que el text quedi per sobre de l’overlay */
.botomenuppal-text span {
    position: relative;
    z-index: 1;
    transition: color .2s ease .1s;
}

/* En hover: text en blanc per contrastar */
.botomenuppal:hover .botomenuppal-text span {
    color: #fff;
}

.botomenuppal-text a {
    text-decoration: none !important;
}


</style>

<script>
document.addEventListener('click', function(event) {
    // Tanca menús desplegables
    document.querySelectorAll('.botomenuppal-menu').forEach(menu => menu.classList.remove('visible'));

    // Tanca confirmacions obertes si es fa clic fora
    document.querySelectorAll('.message.alert').forEach(msg => {
        if (!msg.classList.contains('hidden')) {
            msg.classList.add('hidden');
        }
    });

    // Si has clicat en un <li data-url="..."> dins un menú
    const target = event.target.closest('li[data-url]');
    if (target) {
        const url = target.getAttribute('data-url');
        if (url) {
            window.location.href = url;
        }
    }
});
</script>

