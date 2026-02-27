<?php
/**
 * Element: horaripreinscripcio
 *
 * Igual que l'element horarisatencio, però el rang de dies és
 * [datainicipreinscripcio .. datafipreinscripcio] (ambdós inclosos),
 * prenent la data màxima de cada camp a Years.
 */

declare(strict_types=1);

use Cake\ORM\TableRegistry;

require_once __DIR__ . '/_pagines_dynamic_utils.php';

$daysCa = [
    1 => 'dilluns',
    2 => 'dimarts',
    3 => 'dimecres',
    4 => 'dijous',
    5 => 'divendres',
    6 => 'dissabte',
    7 => 'diumenge',
];

$start = paginesGetYearMaxDate('datainicipreinscripcio');
$end = paginesGetYearMaxDate('datafipreinscripcio');

if (!$start || !$end || $start > $end) {
    return;
}

// Com a horarisatencio: només dies laborables.
$dates = [];
for ($d = $start; $d <= $end; $d = $d->modify('+1 day')) {
    $dow = (int)$d->format('N');
    if ($dow === 6 || $dow === 7) {
        continue;
    }

    $date = \Cake\I18n\FrozenDate::parseDate($d->format('Y-m-d'));
    if ($date) {
        $dates[] = $date;
    }
}

if (empty($dates)) {
    return;
}

$weekdaysInRange = array_values(array_unique(array_map(fn($x) => (int)$x->format('N'), $dates)));

$Horaris = TableRegistry::getTableLocator()->get('Horarisatencio');

$rows = $Horaris->find()
    ->contain(['Days'])
    ->where([
        'OR' => [
            [
                'Horarisatencio.specialdate >=' => $start,
                'Horarisatencio.specialdate <=' => $end,
            ],
            [
                'Horarisatencio.specialdate IS' => null,
                'Horarisatencio.day_id IN' => $weekdaysInRange,
            ],
        ],
    ])
    ->order([
        'Horarisatencio.specialdate' => 'ASC',
        'Horarisatencio.day_id' => 'ASC',
        'Horarisatencio.horainici' => 'ASC',
        'Horarisatencio.horafinal' => 'ASC',
    ])
    ->all();

$itemsByDate = [];
foreach ($dates as $d) {
    $k = $d->format('Y-m-d');
    $itemsByDate[$k] = [
        'date' => $d,
        'hasSpecial' => false,
        'specialTimes' => [],
        'regularTimes' => [],
    ];
}

$fmtTime = function ($t): string {
    if (empty($t)) {
        return '';
    }
    try {
        return $t->i18nFormat('HH:mm');
    } catch (\Throwable $e) {
        return substr((string)$t, 0, 5);
    }
};

foreach ($rows as $r) {
    if (empty($r->specialdate)) {
        continue;
    }

    $k = $r->specialdate->format('Y-m-d');
    if (!isset($itemsByDate[$k])) {
        continue;
    }

    $slot = trim($fmtTime($r->horainici) . '-' . $fmtTime($r->horafinal), '-');
    if ($slot !== '' && $slot !== '-') {
        $itemsByDate[$k]['specialTimes'][] = $slot;
    }
    $itemsByDate[$k]['hasSpecial'] = true;
}

foreach ($rows as $r) {
    if (!empty($r->specialdate)) {
        continue;
    }

    $dayId = (int)($r->day_id ?? 0);
    if ($dayId < 1 || $dayId > 7) {
        continue;
    }

    $slot = trim($fmtTime($r->horainici) . '-' . $fmtTime($r->horafinal), '-');
    if ($slot === '' || $slot === '-') {
        continue;
    }

    foreach ($dates as $d) {
        if ((int)$d->format('N') !== $dayId) {
            continue;
        }

        $k = $d->format('Y-m-d');
        if (!isset($itemsByDate[$k])) {
            continue;
        }
        if ($itemsByDate[$k]['hasSpecial']) {
            continue;
        }

        $itemsByDate[$k]['regularTimes'][] = $slot;
    }
}

foreach ($itemsByDate as $k => $info) {
    $special = array_values(array_unique(array_filter($info['specialTimes'], fn($t) => $t !== '' && $t !== '-')));
    $regular = array_values(array_unique(array_filter($info['regularTimes'], fn($t) => $t !== '' && $t !== '-')));

    sort($special, SORT_NATURAL);
    sort($regular, SORT_NATURAL);

    $itemsByDate[$k]['specialTimes'] = $special;
    $itemsByDate[$k]['regularTimes'] = $regular;
}
?>
<table class="horarisatencio-table" style="border-collapse:collapse; width:auto;">
    <tbody>
        <?php
        $prevDate = null;

        foreach ($itemsByDate as $k => $info):
            $d = $info['date'];
            $dow = (int)$d->format('N');

            $isWeekSeparator = ($dow === 1 && $prevDate !== null);

            $dayName = $daysCa[$dow] ?? strtolower($d->i18nFormat('EEEE'));
            $asterisk = $info['hasSpecial'] ? '*' : '';
            $dayLabel = $dayName . $asterisk . ' ' . $d->format('j');

            $times = $info['hasSpecial'] ? $info['specialTimes'] : $info['regularTimes'];
            $timesText = !empty($times) ? implode("\n", $times) : __('Tancat');

            $tdBase = 'padding:2px 0; border:none; vertical-align:top;';
            $sepStyle = $isWeekSeparator ? 'border-top:1px solid rgba(0,0,0,0.2); padding-top:5px;' : '';
        ?>
            <tr>
                <td style="<?= $tdBase ?> text-align:right; font-weight:700; padding-right:8px; <?= $sepStyle ?>">
                    <?= h($dayLabel) ?>
                </td>
                <td style="<?= $tdBase ?> text-align:left; <?= $sepStyle ?>">
                    <?= nl2br(h($timesText)) ?>
                </td>
            </tr>
        <?php
            $prevDate = $d;
        endforeach;
        ?>
    </tbody>
</table>
