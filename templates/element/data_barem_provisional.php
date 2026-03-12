<?php
declare(strict_types=1);

require_once __DIR__ . '/_pagines_dynamic_utils.php';

echo '<div style="text-align:center;">' . h(paginesFormatCatalanDate(paginesGetYearMaxDate('databaremprovisional'))) . '</div>';
