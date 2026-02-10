<?php
// File: templates/element/botoimgtext.php

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

<div class="botoimgtext-wrapper">
    <?php if (empty($menu)): ?>
    <?php if ($confirm): ?>
        <div class="botoimgtext" style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;" onclick="document.getElementById('<?= $confirmId ?>').classList.remove('hidden'); event.stopPropagation();">
            <div class="botoimgtext-inner">
                <div class="botoimgtext-img">
                    <?= $this->Html->image($image, ['alt' => 'icon']) ?>
                </div>
                <div class="botoimgtext-text">
                    <?= h($text) ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <a href="<?= h($url) ?>"<?= $targetAttr . $titleAttr . $onclickAttr ?> class="botoimgtext" style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;" onclick="event.stopPropagation();">
            <div class="botoimgtext-inner">
                <div class="botoimgtext-img">
                    <?= $this->Html->image($image, ['alt' => 'icon']) ?>
                </div>
                <div class="botoimgtext-text">
                    <?= h($text) ?>
                </div>
            </div>
        </a>
    <?php endif; ?>
    <?php if ($confirm): ?>
        <div id="<?= $confirmId ?>" class="message alert hidden">
            <?= h($confirm) ?>
            <div class="contenidor divcentre separacio">
                <div class="botomsg fons-rosa augmenta-hover" onclick="window.location.href = '<?= h($url) ?>'">
                    <?= __("Confirmar") ?>
                </div>
                <div class="botomsg fons-gris augmenta-hover" onclick="document.getElementById('<?= $confirmId ?>').classList.add('hidden')">
                    <?= __("Cancel·lar") ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php else: ?>
        <div class="botoimgtext" style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;" onclick="document.getElementById('<?= $menuId ?>').classList.toggle('visible'); event.stopPropagation();">
            <div class="botoimgtext-inner">
                <div class="botoimgtext-img">
                    <?= $this->Html->image($image, ['alt' => 'icon']) ?>
                </div>
                <div class="botoimgtext-text">
                    <?= h($text) ?>
                </div>
            </div>
        </div>
        <div class="botoimgtext-menu" id="<?= $menuId ?>">
            <ul>
                <?php foreach ($menu as $item): ?>
                        <?php if (isset($item['submenu'])): ?>
                           <li><span class="submenu-toggle"><?= h($item['label']) ?></span></li>
                            <ul class="submenu" style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;">
                                <?php foreach ($item['submenu'] as $sub): ?>
                                    <li data-url="<?= h($this->Url->build($sub['url'])) ?>"><?= h($sub['label']) ?></li>
                                    <?php echo($sub['label']); ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <li data-url="<?= h($this->Url->build($item['url'])) ?>"><?= h($item['label']) ?></li>
                        <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<style>
.botoimgtext-wrapper {
    width: 100%;
    position: relative;
    display: flex;
    align-items: center;
}
.botoimgtext {
    display: block;
    text-decoration: none;
    text-align: center;
    background-color: var(--bg-color);
    padding: 1vh;
    width: 100%;
    box-sizing: border-box;
    transition: background-color 0.3s;
    cursor: pointer;
    align-items: center;
}

.botoimgtext a {
    text-decoration: none;
}

.botoimgtext:hover {
    background-color: var(--hover-color);

}
.botoimgtext-inner {
    display: flex;
    align-items: center;
    min-height: 1.7rem;
    min-width: 7rem;
    align-items: center;
}
.botoimgtext-img {
    flex: 0 0 15%;
    max-width: 15%;
    padding-right: 1vh;
    text-align: center;
}
.botoimgtext-img img {
    max-width: 100%;
    height: auto;
}
.botoimgtext-text {
    text-transform: uppercase;
    color: white;
    flex: 1;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    font-size: 0.7rem;
    font-weight: bold;
    line-height: 1.2;
}
.botoimgtext-menu-toggle {
    position: absolute;
    top: 1vh;
    right: 1vh;
    cursor: pointer;
    font-weight: bold;
}
.botoimgtext-menu {
    font-size: 1.2vh;
    line-height: 1.2;
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff !important;
    margin-top: 0.3vh;
    padding: 1vh;
    z-index: 1000;
    background-color: var(--bg-color);
    border: 4px solid #e55381; /* Bordes suaus al menú */
    padding: 0.1rem; /* Mida de les opcions */
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);

}
.botoimgtext-menu.visible {
    display: block;
}
.botoimgtext-menu ul {
    list-style: none;
    padding: 0;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

.submenu {
    margin-top: 1vh !important;
    margin-left: 2vh;
}

.botoimgtext-menu span{
    margin-top: 3vh !important;
}

.submenu-toggle:hover + .submenu {
    display: block;
}

.botoimgtext-menu li,
.botoimgtext-menu ul {
    cursor: pointer;
}

.submenu-toggle {
    cursor: pointer;
}


.botoimgtext-menu li {
    padding: 1vh 1vh;
    background-color: var(--bg-color);
    transition: background-color 0.2s, color 0.2s;
}

.botoimgtext-menu li:hover {
    background-color: #9999;
}

.botoimgtext-menu a {
    color: inherit;
    text-decoration: none;
    display: block;
}

.botoimgtext-menu a:hover {
    color: black;
}

.submenu a {
    color: white;
    text-decoration: none;
}

.submenu a:hover {
    color: black;
    background-color: var(--hover-color);
}


</style>

<script>
document.addEventListener('click', function(event) {
    // Tanca menús desplegables
    document.querySelectorAll('.botoimgtext-menu').forEach(menu => menu.classList.remove('visible'));

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

