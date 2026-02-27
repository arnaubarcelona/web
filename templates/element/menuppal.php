<?php
/**
 * Element: menuppal
 *
 * - visible=1
 * - main=1
 * - principals (order_code sense punt)
 * - ordenació natural
 * - màxim 5 botons per fila
 * - espai sobrant repartit
 */

use Cake\ORM\TableRegistry;

$Pagines = TableRegistry::getTableLocator()->get('Pagines');

$pages = $Pagines->find()
    ->select(['id', 'title', 'description', 'order_code', 'link'])
    ->where([
        'visible' => 1,
        'main' => 1,
    ])
    ->andWhere(['order_code NOT LIKE' => '%.%'])
    ->all()
    ->toList();

/* Ordenació natural */
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
<div class="pagina-component">
<div class="menuppal-wrapper menuppal-component">
<?php foreach ($pages as $p): ?>
    <?php
        $color = $colors[$i % count($colors)];
        $i++;

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
</div>

<style>

/* =========================
   FLEX LAYOUT (max 5 per fila)
========================= */

.menuppal-wrapper{
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: center;
}

/* 5 per fila màxim */
.menuppal-wrapper > a.custom-button{
  flex: 0 0 20%;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 1200px){
  .menuppal-wrapper > a.custom-button{
    flex: 0 1 calc(25% - 0.8rem); /* 4 per fila */
  }
}

@media (max-width: 900px){
  .menuppal-wrapper > a.custom-button{
    flex: 0 1 calc(50% - 0.8rem); /* 2 per fila */
    max-width: none;
  }
}

@media (max-width: 600px){
  .menuppal-wrapper{
    justify-content: flex-start;
  }
  .menuppal-wrapper > a.custom-button{
    flex: 0 1 100%;
  }
}

/* =========================
   ANIMACIÓ ENTRADA
========================= */

@keyframes menuPpalSlideInLeft{
  0%   { opacity: 0; transform: translateX(-40px); }
  100% { opacity: 1; transform: translateX(0); }
}

.menuppal-wrapper > a.custom-button{
  opacity: 0;
  animation: menuPpalSlideInLeft 520ms cubic-bezier(.2,.8,.2,1) both;
}

<?php for ($d = 1; $d <= 20; $d++): ?>
.menuppal-wrapper > a.custom-button:nth-child(<?= $d ?>){
  animation-delay: <?= ($d - 1) * 120 ?>ms;
}
<?php endfor; ?>

@media (prefers-reduced-motion: reduce){
  .menuppal-wrapper > a.custom-button{
    opacity: 1;
    animation: none;
  }
}


</style>