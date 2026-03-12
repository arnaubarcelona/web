<?php
declare(strict_types=1);

require_once __DIR__ . '/_pagines_dynamic_utils.php';

$inici = paginesGetYearMaxDate('datainicipreinscripcio');
$fi = paginesGetYearMaxDate('datafipreinscripcio');

if (!$inici || !$fi) {
    return;
}

echo h(paginesNoWrapText(sprintf('del %s al %s', paginesFormatCatalanDate($inici), paginesFormatCatalanDate($fi))));
