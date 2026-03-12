<?php
declare(strict_types=1);

require_once __DIR__ . '/_pagines_dynamic_utils.php';

$date = paginesGetYearMaxDate('datasegonamatricula');
if (!$date) {
    return;
}

$weekDays = [
    0 => 'diumenge',
    1 => 'dilluns',
    2 => 'dimarts',
    3 => 'dimecres',
    4 => 'dijous',
    5 => 'divendres',
    6 => 'dissabte',
];

$months = [
    1 => 'gener',
    2 => 'febrer',
    3 => 'març',
    4 => 'abril',
    5 => 'maig',
    6 => 'juny',
    7 => 'juliol',
    8 => 'agost',
    9 => 'setembre',
    10 => 'octubre',
    11 => 'novembre',
    12 => 'desembre',
];

$weekday = $weekDays[(int)$date->format('w')] ?? '';
$day = (int)$date->format('j');
$month = $months[(int)$date->format('n')] ?? '';

echo '<span style="display:table; margin:0 auto; text-align:left;">' . h(trim(sprintf('%s %d %s', $weekday, $day, $month))) . '</span>';
