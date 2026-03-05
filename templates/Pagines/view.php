<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pagina $pagina
 */

$this->assign('title', $pagina->title ?? 'Pàgina');

$body = (string)($pagina->body ?? '');
$isMenuPpalPage = str_contains($body, '{menuppal}') || str_contains($body, '&#123;menuppal&#125;');

if ($isMenuPpalPage) {
    $this->assign('appMainClass', 'has-menuppal app-main--centered');
}

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

        $elementHtml = $this->element($elementName);

        return sprintf(
            "<div class=\"pagina-element pagina-element--%s\">%s</div>",
            h($elementName),
            $elementHtml
        );
    }, $body);
}
?>

<div class="pagines view content">

    <div class="pagina-body pagina-body-main">
        <?= $this->Html->div(null, $body, ['escape' => false]) ?>
    </div>

</div>

<script>
(function () {
    const MOBILE_QUERY = window.matchMedia('(max-width: 900px)');

    function getHeaderLabels(table) {
        const labels = [];
        const headerCells = table.querySelectorAll('thead th');

        if (headerCells.length > 0) {
            headerCells.forEach((cell) => labels.push((cell.textContent || '').trim()));
            return labels;
        }

        const firstRow = table.querySelector('tr');
        if (!firstRow) {
            return labels;
        }

        firstRow.querySelectorAll('th, td').forEach((cell) => {
            labels.push((cell.textContent || '').trim());
        });

        return labels;
    }

    function applyResponsiveTables() {
        const tables = document.querySelectorAll('.pagina-body table, .pagina-body-main table');

        tables.forEach((table) => {
            table.classList.remove('table-stack-mobile');
            table.classList.remove('table-stack-mobile--no-thead');
            table.querySelectorAll('td, th').forEach((cell) => {
                cell.removeAttribute('data-label');
            });

            if (!MOBILE_QUERY.matches) {
                return;
            }

            const labels = getHeaderLabels(table);
            const rows = table.querySelectorAll('tr');
            const hasThead = table.querySelector('thead') !== null;
            table.classList.toggle('table-stack-mobile--no-thead', !hasThead);

            rows.forEach((row, rowIndex) => {
                if (!hasThead && rowIndex === 0) {
                    return;
                }

                row.querySelectorAll('th, td').forEach((cell, index) => {
                    const label = labels[index] || '';
                    cell.setAttribute('data-label', label);
                });
            });

            table.classList.add('table-stack-mobile');
        });
    }

    window.addEventListener('resize', applyResponsiveTables);
    window.addEventListener('load', applyResponsiveTables);
    applyResponsiveTables();
})();
</script>
