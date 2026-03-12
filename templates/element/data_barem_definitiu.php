<?php
declare(strict_types=1);

require_once __DIR__ . '/_pagines_dynamic_utils.php';

echo '<span style="display:table; margin:0 auto; text-align:left;">' . h(paginesFormatCatalanDate(paginesGetYearMaxDate('databaremdefinitius'))) . '</span>';
