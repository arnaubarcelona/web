<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pagina $pagina
 */

$this->assign('title', $pagina->title ?? 'Pàgina');

$body = (string)($pagina->body ?? '');

if ($body !== '') {
    // Regex que captura:
    //  - {element}
    //  - &#123;element&#125;
    $pattern = '/(?:\{|\&\#123;)\s*([a-zA-Z0-9_-]+)\s*(?:\}|\&\#125;)/';

    $body = preg_replace_callback($pattern, function ($m) {
        $elementName = $m[1];

        if ($elementName === '' || str_contains($elementName, '..') || str_contains($elementName, '/')) {
            return $m[0];
        }

        $path = ROOT . DS . 'templates' . DS . 'element' . DS . $elementName . '.php';
        if (!is_file($path)) {
            return $m[0];
        }

        return $this->element($elementName);
    }, $body);
}
?>

<div class="pagines view content">

    <h1><?= h($pagina->title) ?></h1>

    <?php if (!empty($pagina->description)): ?>
        <p class="pagina-description"><?= h($pagina->description) ?></p>
    <?php endif; ?>

    <div class="pagina-body">
        <?= $this->Html->div(null, $body, ['escape' => false]) ?>
    </div>

</div>
