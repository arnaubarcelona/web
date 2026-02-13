<?php
/**
 * Element: menuppal
 *
 * Mostra un menú principal en 3 columnes amb botoDoble
 * - Pàgines: visible=1, main=1
 * - Només principals: order_code sense punt
 * - Ordenació natural per order_code
 * - Colors en seqüència: blaucel, blaumari, verd, rosa, lila, taronja, gris, ocre
 */

use Cake\ORM\TableRegistry;

/** @var \App\Model\Table\PaginesTable $Pagines */
$Pagines = TableRegistry::getTableLocator()->get('Pagines');

$pages = $Pagines->find()
    ->select(['id', 'title', 'description', 'order_code', 'link'])
    ->where([
        'visible' => 1,
        'main' => 1,
    ])
    // només order_code sense punt (principals)
    ->andWhere(['order_code NOT LIKE' => '%.%'])
    ->all()
    ->toList();

/**
 * Ordenació "natural" per order_code: 1, 2, 10, 11...
 * (si algun order_code no és numèric, quedarà al final per comparació 0)
 */
usort($pages, function ($a, $b) {
    $va = (int)preg_replace('/\D+/', '', (string)$a->order_code);
    $vb = (int)preg_replace('/\D+/', '', (string)$b->order_code);

    if ($va === $vb) {
        return strcmp((string)$a->order_code, (string)$b->order_code);
    }
    return $va <=> $vb;
});

$colors = ['blaumari', 'blaucel', 'verd', 'rosa', 'lila', 'taronja', 'gris', 'ocre'];
$i = 0;
?>

<div class="menuppal-wrapper">
    <?php foreach ($pages as $p): ?>
        <?php
            $color = $colors[$i % count($colors)];
            $i++;

            // Link: si hi ha link a la DB l’usem; sinó anem a Pagines/view/id
            $link = !empty($p->link)
                ? $p->link
                : ['controller' => 'Pagines', 'action' => 'view', $p->id];

            $desc = trim((string)($p->description ?? ''));
        ?>

        <?= $this->element('botoDoble', [
            'color' => $color,
            'title' => (string)$p->title,
            'text'  => $desc,
            'link'  => $link,
        ]) ?>
    <?php endforeach; ?>
</div>

<style>
.menuppal-wrapper{
    margin-top:5rem !important;
    margin-left: 1rem;
    margin-right: 1rem;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

/* responsive simple */
@media (max-width: 900px){
    .menuppal-wrapper{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 600px){
    .menuppal-wrapper{ grid-template-columns: 1fr; }
}

/* ===== MENU PPAL: animació entrada en cascada ===== */
@keyframes menuPpalSlideInLeft {
  0%   { opacity: 0; transform: translateX(-40px); }
  100% { opacity: 1; transform: translateX(0); }
}

/* Aplica només dins menuppal (i sobrescriu la bounceIn del botoDoble) */
.menuppal-wrapper a.custom-button{
  opacity: 0;
  animation: menuPpalSlideInLeft 520ms cubic-bezier(.2,.8,.2,1) both;
  will-change: transform, opacity;
}

/* Cascada (un rere l'altre) */
.menuppal-wrapper a.custom-button:nth-child(1)  { animation-delay: 0ms; }
.menuppal-wrapper a.custom-button:nth-child(2)  { animation-delay: 140ms; }
.menuppal-wrapper a.custom-button:nth-child(3)  { animation-delay: 280ms; }
.menuppal-wrapper a.custom-button:nth-child(4)  { animation-delay: 420ms; }
.menuppal-wrapper a.custom-button:nth-child(5)  { animation-delay: 560ms; }
.menuppal-wrapper a.custom-button:nth-child(6)  { animation-delay: 700ms; }
.menuppal-wrapper a.custom-button:nth-child(7)  { animation-delay: 840ms; }
.menuppal-wrapper a.custom-button:nth-child(8)  { animation-delay: 980ms; }
.menuppal-wrapper a.custom-button:nth-child(9)  { animation-delay: 1120ms; }
.menuppal-wrapper a.custom-button:nth-child(10) { animation-delay: 1260ms; }
.menuppal-wrapper a.custom-button:nth-child(11) { animation-delay: 1400ms; }
.menuppal-wrapper a.custom-button:nth-child(12) { animation-delay: 1540ms; }
.menuppal-wrapper a.custom-button:nth-child(13) { animation-delay: 1680ms; }
.menuppal-wrapper a.custom-button:nth-child(14) { animation-delay: 1820ms; }
.menuppal-wrapper a.custom-button:nth-child(15) { animation-delay: 1960ms; }
.menuppal-wrapper a.custom-button:nth-child(16) { animation-delay: 2100ms; }
.menuppal-wrapper a.custom-button:nth-child(17) { animation-delay: 2240ms; }
.menuppal-wrapper a.custom-button:nth-child(18) { animation-delay: 2380ms; }
.menuppal-wrapper a.custom-button:nth-child(19) { animation-delay: 2520ms; }
.menuppal-wrapper a.custom-button:nth-child(20) { animation-delay: 2660ms; }

/* Respecta prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  .menuppal-wrapper a.custom-button{
    opacity: 1;
    animation: none;
  }
}

</style>
