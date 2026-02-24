<?php
/**
 * Element: pestanya
 *
 * Params:
 * - titol (string)        : text del títol
 * - contingut (string)    : HTML del contingut (llista, paràgrafs, etc.)
 * - color (string)        : rosa|blaucel|lila|taronja|blaumari|verd|gris|ocre|grisclar
 * - extraClass (string)   : classes addicionals opcionals (p.ex. "tic")
 *
 * Exemple:
 * echo $this->element('pestanya', [
 *   'titol' => 'LLENGUA CATALANA 1',
 *   'contingut' => '<ul><li>Nivell A1</li></ul>',
 *   'color' => 'blaucel',
 * ]);
 */

$titol = $titol ?? '';
$titolHtml = $titolHtml ?? '';
$contingut = $contingut ?? '';
$color = $color ?? 'grisclar';
$extraClass = $extraClass ?? '';

$colorsOk = ['rosa','blaucel','lila','taronja','blaumari','verd','gris','ocre','grisclar'];
if (!in_array($color, $colorsOk, true)) {
    $color = 'grisclar';
}

$classes = trim("pestanya pestanya-{$color} {$extraClass}");
?>

<div class="<?= h($classes) ?>">
    <div class="pestanya-capcalera">
        <div class="titol"><?= $titolHtml !== '' ? $titolHtml : h($titol) ?></div>
        <div class="pestanya-nav" aria-label="Navegació de pestanyes">
            <button type="button" class="pestanya-nav-btn pestanya-nav-btn--prev" aria-label="Pestanya anterior">&#9664;</button>
            <button type="button" class="pestanya-nav-btn pestanya-nav-btn--next" aria-label="Pestanya següent">&#9654;</button>
        </div>
    </div>
    <div class="text"><?= $contingut ?></div>
</div>

<script>
(function () {
    if (window.__pestanyaNavReady) {
        return;
    }
    window.__pestanyaNavReady = true;

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.pestanya-nav-btn');
        if (!button) {
            return;
        }

        const tabItem = button.closest('.cursos-tab-item');
        if (!tabItem) {
            return;
        }

        const direction = button.classList.contains('pestanya-nav-btn--prev') ? -1 : 1;
        let sibling = tabItem;

        do {
            sibling = direction < 0 ? sibling.previousElementSibling : sibling.nextElementSibling;
        } while (sibling && !sibling.classList.contains('cursos-tab-item'));

        if (!sibling) {
            sibling = direction < 0
                ? tabItem.parentElement.querySelector('.cursos-tab-item:last-child')
                : tabItem.parentElement.querySelector('.cursos-tab-item:first-child');
        }

        sibling?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
