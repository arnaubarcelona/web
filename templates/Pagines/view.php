<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pagina $pagina
 */

$this->assign('title', $pagina->title ?? 'Pàgina');

$body = (string)($pagina->body ?? '');
$isMenuPpalPage = str_contains($body, '{menuppal}') || str_contains($body, '&#123;menuppal&#125;');

$renderDynamicElements = function (string $html): string {
    if ($html === '') {
        return $html;
    }

    $pattern = '/(?:\{|\&\#123;)\s*([a-zA-Z0-9_-]+)\s*(?:\}|\&\#125;)/';

    return preg_replace_callback($pattern, function ($m) {
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
            '<div class="pagina-element pagina-element--%s">%s</div>',
            h($elementName),
            $elementHtml
        );
    }, $html);
};

$popupBody = '';

if ($isMenuPpalPage) {
    $this->assign('appMainClass', 'has-menuppal app-main--centered');

    $Pagines = \Cake\ORM\TableRegistry::getTableLocator()->get('Pagines');
    $popupPagina = $Pagines->find()
        ->select(['body'])
        ->where(['popup' => 1])
        ->order(['id' => 'DESC'])
        ->first();

    if ($popupPagina !== null) {
        $popupBody = (string)($popupPagina->body ?? '');
        $popupBody = $renderDynamicElements($popupBody);
    }
}

$body = $renderDynamicElements($body);
?>

<div class="pagines view content">

    <div class="pagina-body pagina-body-main">
        <?= $this->Html->div(null, $body, ['escape' => false]) ?>
    </div>

</div>

<?php if ($popupBody !== ''): ?>
    <div class="popup-menuppal" id="popupMenuppal" role="dialog" aria-modal="true" aria-label="Avís important">
        <div class="popup-menuppal__scroll">
            <div class="popup-menuppal__content">
                <?= $this->Html->div(null, $popupBody, ['escape' => false]) ?>
                <div class="popup-menuppal__actions">
                    <button type="button" class="popup-menuppal__close" id="popupMenuppalClose">TANCA</button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .popup-menuppal {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        width: 100vw;
        height: 100vh;
        z-index: 2147483647 !important;
        background: #e55381;
        color: #fff;
        display: flex;
        padding: 2rem;
        isolation: isolate;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .popup-menuppal__scroll {
        width: 100%;
        min-height: calc(100vh - 4rem);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .popup-menuppal__content {
        width: 100%;
        max-width: 1100px;
        max-height: 100%;
        overflow: auto;
        font-family: "Roboto Condensed", "Roboto", sans-serif;
        font-size: 2rem;
        line-height: 1.25;
        color: #fff;
    }

    .popup-menuppal__actions {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: 1.25rem;
    }

    .popup-menuppal__content table:not([border]):not([style*="border"]),
    .popup-menuppal__content table:not([border]):not([style*="border"]) td:not([style*="border"]),
    .popup-menuppal__content table:not([border]):not([style*="border"]) th:not([style*="border"]) {
        border: 0 !important;
    }

    .popup-menuppal__content h1,
    .popup-menuppal__content h2,
    .popup-menuppal__content h3 {
        width: 100%;
        margin: 0 0 1rem 0;
        padding: 0.5em;
        background: #708090;
        color: #fff;
        font-family: "Bebas Neue", sans-serif;
        font-size: 3rem;
        line-height: 1;
    }

    .popup-menuppal__close {
        border: 0;
        background: #708090;
        color: #fff;
        font-family: "Bebas Neue", sans-serif;
        font-size: 1.25rem;
        padding: 0.35rem 0.75rem;
        cursor: pointer;
    }

    @media (max-width: 900px) {
        .popup-menuppal__content {
            font-size: 1rem;
        }

        .popup-menuppal__content h1,
        .popup-menuppal__content h2,
        .popup-menuppal__content h3 {
            font-size: 2rem;
        }
    }
    </style>

    <script>
    (function () {
        const modal = document.getElementById('popupMenuppal');
        const closeBtn = document.getElementById('popupMenuppalClose');
        if (!modal || !closeBtn) {
            return;
        }

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        closeBtn.addEventListener('click', function () {
            modal.remove();
        });
    })();
    </script>
<?php endif; ?>

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
