<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pagina $pagina
 */

$this->assign('title', $pagina->title ?? 'Pàgina');
?>

<div class="pagines view content">

    <h1><?= h($pagina->title) ?></h1>

    <?php if (!empty($pagina->description)): ?>
        <p class="pagina-description"><?= h($pagina->description) ?></p>
    <?php endif; ?>

    <div class="pagina-body">
        <?= $this->Html->div(null, (string)($pagina->body ?? ''), ['escape' => false]) ?>
    </div>

</div>
