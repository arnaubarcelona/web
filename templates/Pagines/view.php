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
        }

        return labels;
    }

    function isComplexTable(table) {
        if (table.querySelector('[rowspan], [colspan]')) {
            return true;
        }

        const rows = Array.from(table.querySelectorAll('tr'));
        const dataRows = rows.filter((row) => row.closest('thead') === null);
        const firstDataRow = dataRows[0] || rows[0] || null;
        if (!firstDataRow) {
            return false;
        }

        const expectedCells = firstDataRow.querySelectorAll('th, td').length;
        if (expectedCells === 0) {
            return false;
        }

        return dataRows.some((row) => row.querySelectorAll('th, td').length !== expectedCells);
    }

    function wrapScrollableTable(table) {
        const parent = table.parentElement;
        if (!parent || parent.classList.contains('table-mobile-scroll-wrap')) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'table-mobile-scroll-wrap';
        parent.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    }

    function unwrapScrollableTable(table) {
        const parent = table.parentElement;
        if (!parent || !parent.classList.contains('table-mobile-scroll-wrap')) {
            return;
        }

        parent.replaceWith(table);
    }

    function applyResponsiveTables() {
        const tables = document.querySelectorAll('.pagina-body table, .pagina-body-main table');

        tables.forEach((table) => {
            const isElementTable = table.closest('.pagina-element') !== null;

            table.classList.remove('pagina-table-managed');
            table.classList.remove('table-stack-mobile');
            table.classList.remove('table-mobile-scroll');
            table.classList.remove('table-stack-mobile--plain-rows');
            table.querySelectorAll('td, th').forEach((cell) => {
                cell.removeAttribute('data-label');
            });

            unwrapScrollableTable(table);

            if (isElementTable) {
                return;
            }

            table.classList.add('pagina-table-managed');

            if (!MOBILE_QUERY.matches) {
                return;
            }

            const hasThead = table.querySelector('thead') !== null;

            if (!hasThead) {
                table.classList.add('table-stack-mobile', 'table-stack-mobile--plain-rows');
                return;
            }

            if (isComplexTable(table)) {
                table.classList.add('table-mobile-scroll');
                wrapScrollableTable(table);
                return;
            }

            const labels = getHeaderLabels(table);
            const rows = table.querySelectorAll('tr');

            rows.forEach((row) => {
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
