<?php
// File: templates/element/bototext.php

use Cake\Routing\Router;

$text = $text ?? '';
$link = $link ?? '#';
$target = $target ?? null;
$title = $title ?? '';
$menu = $menu ?? [];
$color = $color ?? 'gris';
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

<div class="bototext-wrapper">
<?php if (empty($menu)): ?>

    <?php if ($confirm): ?>
        <div class="bototext"
             style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;"
             onclick="document.getElementById('<?= $confirmId ?>').classList.remove('hidden'); event.stopPropagation();">
            <div class="bototext-inner">
                <div class="bototext-text"><?= h($text) ?></div>
            </div>
        </div>
    <?php else: ?>
        <a href="<?= h($url) ?>"<?= $targetAttr . $titleAttr . $onclickAttr ?>
           class="bototext"
           style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;"
           onclick="event.stopPropagation();">
            <div class="bototext-inner">
                <div class="bototext-text"><?= h($text) ?></div>
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

    <div class="bototext"
         style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;"
         onclick="document.getElementById('<?= $menuId ?>').classList.toggle('visible'); event.stopPropagation();">
        <div class="bototext-inner">
            <div class="bototext-text"><?= h($text) ?></div>
        </div>
    </div>

    <div class="bototext-menu" id="<?= $menuId ?>">
        <ul>
            <?php foreach ($menu as $item): ?>
                <?php if (isset($item['submenu'])): ?>
                    <li><span class="submenu-toggle"><?= h($item['label']) ?></span></li>
                    <ul class="submenu" style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;">
                        <?php foreach ($item['submenu'] as $sub): ?>
                            <li data-url="<?= h($this->Url->build($sub['url'])) ?>"><?= h($sub['label']) ?></li>
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
.bototext-wrapper {
    width: 100%;
    position: relative;
    display: flex;
    align-items: center;
}

.bototext {
    display: block;
    text-decoration: none;
    background-color: var(--bg-color);
    padding: 1vh;
    width: 100%;
    box-sizing: border-box;
    transition: background-color 0.3s;
    cursor: pointer;
}

.bototext:hover {
    background-color: var(--hover-color);
}

.bototext-inner {
    display: flex;
    align-items: center;
    min-height: 1.7rem;
    min-width: 7rem;
}

.bototext-text {
    text-transform: uppercase;
    color: white;
    width: 100%;
    text-align: left;               /* <-- ESQUERRA */
    font-family: 'Bebas Neue', Bebas, Arial, sans-serif; /* <-- BEBAS */
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    font-size: 0.95rem;
    font-weight: 400;
    line-height: 1.2;
}

/* Menu desplegable (mateix estil que botoimgtext) */
.bototext-menu {
    font-size: 1.2vh;
    line-height: 1.2;
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff !important;
    margin-top: 0.3vh;
    z-index: 1000;
    background-color: var(--bg-color);
    border: 4px solid #e55381;
    padding: 0.1rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.bototext-menu.visible { display: block; }

.bototext-menu ul {
    list-style: none;
    padding: 0;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

.submenu {
    margin-top: 1vh !important;
    margin-left: 2vh;
}

.submenu-toggle { cursor: pointer; }
.submenu-toggle:hover + .submenu { display: block; }

.bototext-menu li,
.bototext-menu ul { cursor: pointer; }

.bototext-menu li {
    padding: 1vh 1vh;
    background-color: var(--bg-color);
    transition: background-color 0.2s, color 0.2s;
    color: white;
    font-family: 'Bebas Neue', Bebas, Arial, sans-serif;
    text-transform: uppercase;
}

.bototext-menu li:hover { background-color: #9999; color: black; }
</style>

<script>
document.addEventListener('click', function(event) {
    // Tanca menús desplegables
    document.querySelectorAll('.bototext-menu').forEach(menu => menu.classList.remove('visible'));

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
        if (url) window.location.href = url;
    }
});
</script>
