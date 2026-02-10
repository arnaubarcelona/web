<?php
// File: templates/element/menucursos.php

use Cake\Routing\Router;

// Variables requerides:
// - $yearsWithCourses (array amb estructura [yearName => [course1, course2, ...]])

$color = $color ?? 'blaumari';
$text = $text ?? 'Grups';
$image = $image ?? 'grups.png';
$title = $title ?? 'Gestió de grups-classe';

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
$menuId = 'menu_' . uniqid();
?>

<div class="botocursos-wrapper">
    <div class="botocursos" style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;" onclick="document.getElementById('<?= $menuId ?>').classList.toggle('visible'); event.stopPropagation();">
        <div class="botocursos-inner">
            <div class="botocursos-img">
                <?= $this->Html->image($image, ['alt' => 'icon']) ?>
            </div>
            <div class="botocursos-text">
                <?= h($text) ?>
            </div>
        </div>
    </div>
    <div class="botocursos-menu" style="--bg-color: <?= h($bgColorCode) ?>; --hover-color: <?= h($hoverColor) ?>;" id="<?= $menuId ?>">
        <ul>
            <?php foreach ($yearsWithCourses as $yearName => $courses): ?>
                <li><span class="submenu-toggle"><?= h($yearName) ?></span></li>
                <ul class="submenu-columns">
                    <?php if (!empty($courses)): ?>
                        <li data-url="<?= h($this->Url->build(['controller' => 'Courses', 'action' => 'index', $courses[0]->year_id])) ?>">
                            <strong>TOTS ELS GRUPS</strong>
                        </li>
                    <?php endif; ?>
                    <?php foreach ($courses as $course): ?>
                        <li data-url="<?= h($this->Url->build(['controller' => 'Courses', 'action' => 'view', $course->id])) ?>">
                            <?php
                            $nom = $course->name;

                            // Substitucions personalitzades
                            $nom = str_replace("PROVES D'ACCÉS A CF DE GRAU MITJÀ", 'PPACFGM', $nom);
                            $nom = str_replace(' - ', ' ', $nom);

                            // Salt de línia abans de MATÍ, TARDA, VESPRE (amb espais opcionals davant)
                            $nom = preg_replace('/\\s?(MATÍ|TARDA|VESPRE)/u', '<br>$1', $nom);
                            ?>
                            <?= $nom ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<style>
.botocursos-wrapper {
    width: 100%;
    position: relative;
    display: flex;
    align-items: center;
}
.botocursos {
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
.botocursos:hover {
    background-color: var(--hover-color);
}
.botocursos-inner {
    display: flex;
    align-items: center;
    min-height: 1.7rem;
    min-width: 7rem;
}
.botocursos-img {
    flex: 0 0 15%;
    max-width: 15%;
    padding-right: 1vh;
    text-align: center;
}
.botocursos-img img {
    max-width: 100%;
    height: auto;
}
.botocursos-text {
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
.botocursos-menu {
    font-size: 1.2vh;
    line-height: 1.2;
    color: white;
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 0.3vh;
    padding: 1vh;
    z-index: 1000;
    border-color: var(--bg-color);
    border-style: solid;
    border-width: 4px;
    background-color: white;
  max-height: 60vh; /* Limita alçada màxima */
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.submenu-columns {
    column-count: 7;
    column-gap: 1rem;
    padding: 0;
    margin: 0 1vh 1vh 2vh;
}

.submenu-columns li {
    break-inside: avoid;
    margin-bottom: 0.4rem;
    background: none;
    padding: 0.3rem 0.5rem;
}
.botocursos-menu.visible {
    display: block;
}
.botocursos-menu ul {
    list-style: none;
    padding: 0;
    margin-top: 1vh;
}
.submenu-toggle {
    cursor: pointer;
    display: block;
    padding: 0.5vh 0;
    font-weight: bold;
    color: white;
    background-color: var(--bg-color);
    font-weight: bold;
    font-size: 0.9rem;
    padding: 0.2rem;
}
.submenu-columns {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 0.5rem;
    margin: 1vh 0 1vh 2vh;
    padding: 0;
}
.submenu-columns li {
    list-style: none;
    padding: 0.3rem;
    color: black;
    cursor: pointer;
}
.submenu-columns li:hover {
    background-color: #9999;
}
</style>

<script>
document.addEventListener('click', function() {
    document.querySelectorAll('.botocursos-menu').forEach(menu => menu.classList.remove('visible'));
});

document.addEventListener('click', function(event) {
    const target = event.target.closest('li[data-url]');
    if (target) {
        const url = target.getAttribute('data-url');
        if (url) {
            window.location.href = url;
        }
    }
});
</script>
