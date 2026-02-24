<?php
/**
 * Element: horarisatencio
 *
 * Mostra l'horari d'atenció d'avui i dels propers 7 dies (inclou avui = 8 dies),
 * excloent dissabtes i diumenges.
 *
 * Requisits:
 * - Taula sense vores, max-width: 10rem
 * - 1a columna: alineada a la dreta i en negreta
 * - 2a columna: alineada a l'esquerra (no negreta)
 * - Dia + número del mes: "dimarts 26"
 * - Si és specialdate per aquell dia: "dimarts* 26"
 * - Si hi ha 2+ franges el mateix dia: "10:00-13:00 i 16:00-20:00"
 * - Si un dia té specialdate, NO es mostren els recurrents (day_id) d'aquell dia
 * - Si és laborable i no hi ha cap horari aplicable: "Tancat"
 * - Separador setmanal: entre l'últim dia de la setmana (dg) i el primer (dl).
 *   Com que no mostrem caps de setmana, es materialitza com una vora fina abans de cada dilluns (excepte el primer).
 */

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

$daysCa = [
    1 => 'dilluns',
    2 => 'dimarts',
    3 => 'dimecres',
    4 => 'dijous',
    5 => 'divendres',
    6 => 'dissabte',
    7 => 'diumenge',
];

// Rang (avui + 7 dies)
$start = FrozenDate::today();
$end   = $start->addDays(7);

// Dates laborables del rang (dl..dv)
$dates = [];
for ($d = $start; $d <= $end; $d = $d->addDays(1)) {
    $dow = (int)$d->format('N'); // 1..7 (dl..dg)
    if ($dow === 6 || $dow === 7) continue; // exclou ds/dg
    $dates[] = $d;
}
if (empty($dates)) {
    return;
}

// Weekdays presents al rang (normalment 1..5)
$weekdaysInRange = array_values(array_unique(array_map(fn($x) => (int)$x->format('N'), $dates)));

$Horaris = TableRegistry::getTableLocator()->get('Horarisatencio');

// Carreguem:
// - Specialdates dins el rang
// - Recurrents (specialdate null) pels weekdays del rang
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

// Estructura per data
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

// Helper format hora 00:00
$fmtTime = function ($t): string {
    if (empty($t)) return '';
    try {
        return $t->i18nFormat('HH:mm');
    } catch (\Throwable $e) {
        return substr((string)$t, 0, 5);
    }
};

// 1) Indexa primer els specialdate (i marca hasSpecial)
foreach ($rows as $r) {
    if (empty($r->specialdate)) continue;

    $k = $r->specialdate->format('Y-m-d');
    if (!isset($itemsByDate[$k])) continue;

    $slot = trim($fmtTime($r->horainici) . '-' . $fmtTime($r->horafinal), '-');
    if ($slot !== '' && $slot !== '-') {
        $itemsByDate[$k]['specialTimes'][] = $slot;
    }
    $itemsByDate[$k]['hasSpecial'] = true; // encara que no hi hagi franja, anul·la recurrents
}

// 2) Aplica recurrents només als dies que NO tenen specialdate
foreach ($rows as $r) {
    if (!empty($r->specialdate)) continue;

    $dayId = (int)($r->day_id ?? 0);
    if ($dayId < 1 || $dayId > 7) continue;

    $slot = trim($fmtTime($r->horainici) . '-' . $fmtTime($r->horafinal), '-');
    if ($slot === '' || $slot === '-') continue;

    foreach ($dates as $d) {
        if ((int)$d->format('N') !== $dayId) continue;

        $k = $d->format('Y-m-d');
        if (!isset($itemsByDate[$k])) continue;

        // Si hi ha specialdate aquell dia, NO mostrem recurrents
        if ($itemsByDate[$k]['hasSpecial']) continue;

        $itemsByDate[$k]['regularTimes'][] = $slot;
    }
}

// Neteja: elimina duplicats i ordena
foreach ($itemsByDate as $k => $info) {
    $special = array_values(array_unique(array_filter($info['specialTimes'], fn($t) => $t !== '' && $t !== '-')));
    $regular = array_values(array_unique(array_filter($info['regularTimes'], fn($t) => $t !== '' && $t !== '-')));

    sort($special, SORT_NATURAL);
    sort($regular, SORT_NATURAL);

    $itemsByDate[$k]['specialTimes'] = $special;
    $itemsByDate[$k]['regularTimes'] = $regular;
}

?>
<table style="border-collapse:collapse; width:auto;">
    <tbody>
        <?php
        $prevDate = null;

        foreach ($itemsByDate as $k => $info):
            /** @var \Cake\I18n\FrozenDate $d */
            $d = $info['date'];
            $dow = (int)$d->format('N'); // 1..7

            // Separador setmanal: abans de cada dilluns (excepte el primer)
            $isWeekSeparator = ($dow === 1 && $prevDate !== null);

            $dayName = $daysCa[$dow] ?? strtolower($d->i18nFormat('EEEE'));
            $asterisk = $info['hasSpecial'] ? '*' : '';
            $dayLabel = $dayName . $asterisk . ' ' . $d->format('j');

            // Si hi ha specialdate, només specialTimes. Si no, regularTimes.
            $times = $info['hasSpecial'] ? $info['specialTimes'] : $info['regularTimes'];

            // Si no hi ha franges aplicables: dia laborable tancat
            $timesText = !empty($times) ? implode(' i ', $times) : __('Tancat');

            $tdBase = 'padding:4px 0; border:none; vertical-align:top;';
            $sepStyle = $isWeekSeparator ? 'border-top:1px solid rgba(0,0,0,0.2); padding-top:8px;' : '';
        ?>
            <tr>
                <td style="<?= $tdBase ?> text-align:right; font-weight:700; padding-right:12px; <?= $sepStyle ?>">
                    <?= h($dayLabel) ?>
                </td>
                <td style="<?= $tdBase ?> text-align:left; <?= $sepStyle ?>">
                    <?= h($timesText) ?>
                </td>
            </tr>
        <?php
            $prevDate = $d;
        endforeach;
        ?>
    </tbody>
</table>
