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
    <div class="titol"><?= $titolHtml !== '' ? $titolHtml : h($titol) ?></div>
    <div class="text"><?= $contingut ?></div>
</div>
