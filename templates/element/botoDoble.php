<?php
/**
 * Element: botoDoble
 *
 * Params:
 * - color: (string) 'rosa' | 'blaucel' | 'lila' | 'taronja' | 'blaumari' | 'verd' | 'gris' | 'ocre' | 'grisclar'
 * - title: (string) línia 1
 * - text:  (string) línia 2
 * - link:  (mixed) url string o array CakePHP (opcional, per defecte '#')
 */

$color = $color ?? 'rosa';
$title = $title ?? '';
$text  = $text ?? '';
$link  = $link ?? '#';

$colorClassMap = [
  'rosa' => 'botorosa',
  'blaucel' => 'botoblaucel',
  'lila' => 'botolila',
  'taronja' => 'bototaronja',
  'blaumari' => 'botoblaumari',
  'verd' => 'botoverd',
  'gris' => 'botogris',
  'ocre' => 'botoocre',
  'grisclar' => 'botogrisclar',
];

$colorClass = $colorClassMap[$color] ?? 'botorosa';

echo $this->Html->link(
  '<span class="line1">' . h($title) . '</span>' .
  '<span class="line2">' . h($text)  . '</span>',
  $link,
  [
    'escape' => false,
    'class' => 'custom-button ' . $colorClass,
  ]
);
