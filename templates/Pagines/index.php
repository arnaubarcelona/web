<?php
declare(strict_types=1);

/** @var \App\View\AppView $this */
/** @var \Cake\Datasource\ResultSetInterface $pagines */
?>
<div class="pagines index content">
    <aside>
        <div class="side-nav">
            <h4 class="heading"><?= __('Accions') ?></h4>
            <?= $this->Html->link(__('Nova pàgina'), ['action' => 'add'], ['class' => 'bototext']) ?>
            <?= $this->Form->postLink(
                __('Actualitza'),
                ['action' => 'actualitza'],
                [
                    'class' => 'bototext',
                    'confirm' => __('Vols executar la sincronització web ara?'),
                ]
            ) ?>
        </div>
    </aside>

    <h3><?= __('Pàgines') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('title') ?></th>
                    <th><?= $this->Paginator->sort('order_code') ?></th>
                    <th><?= $this->Paginator->sort('visible') ?></th>
                    <th><?= $this->Paginator->sort('main') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Accions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagines as $pagina): ?>
                <tr>
                    <td><?= $this->Number->format($pagina->id) ?></td>
                    <td><?= h($pagina->title) ?></td>
                    <td><?= h($pagina->order_code) ?></td>
                    <td><?= $pagina->visible ? __('Sí') : __('No') ?></td>
                    <td><?= $pagina->main ? __('Sí') : __('No') ?></td>
                    <td><?= h($pagina->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Veure'), ['action' => 'view', $pagina->id]) ?>
                        <?= $this->Html->link(__('Edita'), ['action' => 'edit', $pagina->id]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
