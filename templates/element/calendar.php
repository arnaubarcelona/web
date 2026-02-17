<?php
/**
 * Element: calendar
 *
 * Calendari anual autònom:
 * - Calcula any acadèmic
 * - Carrega festius
 * - Construeix mesos i dies
 * - Mostra llegenda completa
 */

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;

/* ============================
 * 1) ANY ACADÈMIC ACTIU
 * ============================ */
$today = FrozenDate::today();
$Years = TableRegistry::getTableLocator()->get('Years');

$this->Html->css('calendar', ['block' => 'css']);

$year = $Years->find()
    ->where([
        'datainici <=' => $today,
        'datafi >=' => $today,
    ])
    ->order(['datainici' => 'DESC'])
    ->first();

if (!$year) {
    $year = $Years->find()
        ->order(['datafi' => 'DESC'])
        ->first();
}

if (!$year) {
    throw new NotFoundException(__('No academic year found.'));
}

$datainici = FrozenDate::parse($year->datainici);
$datafi    = FrozenDate::parse($year->datafi);

$openStart = FrozenDate::create($datainici->year, 9, 1);
$openEnd   = FrozenDate::create($datafi->year, 7, 15);

/* ============================
 * 2) FESTIUS
 * ============================ */
$Festius = TableRegistry::getTableLocator()->get('Festius');
$festius = $Festius->find()
    ->select(['data'])
    ->where([
        'data >=' => $openStart,
        'data <=' => $openEnd,
    ])
    ->enableHydration(false)
    ->all();

$festiuDates = [];
foreach ($festius as $f) {
    $festiuDates[(string)FrozenDate::parse($f['data'])->format('Y-m-d')] = true;
}

/* ============================
 * 3) FUNCIONS AUXILIARS
 * ============================ */
$dayCell = function (
    FrozenDate $date
) use ($datainici, $datafi, $openStart, $openEnd, $festiuDates): array {

    $key = $date->format('Y-m-d');
    $isWeekend = (int)$date->format('N') >= 6;

    $class = 'calendar-day--closed';

    if ($isWeekend || isset($festiuDates[$key])) {
        $class = 'calendar-day--festiu';
    } elseif ($date >= $datainici && $date <= $datafi) {
        $class = 'calendar-day--lectiu';
    } elseif ($date >= $openStart && $date <= $openEnd) {
        $class = 'calendar-day--obert';
    }

    return [
        'number' => (int)$date->format('j'),
        'class'  => $class,
    ];
};

$monthNames = [
    1 => 'GENER', 2 => 'FEBRER', 3 => 'MARÇ', 4 => 'ABRIL',
    5 => 'MAIG', 6 => 'JUNY', 7 => 'JULIOL', 8 => 'AGOST',
    9 => 'SETEMBRE', 10 => 'OCTUBRE', 11 => 'NOVEMBRE', 12 => 'DESEMBRE',
];

/* ============================
 * 4) CONSTRUCCIÓ MESOS
 * ============================ */
$months = [];
$cursor = $openStart->firstOfMonth();
$endMonth = $openEnd->firstOfMonth();

while ($cursor <= $endMonth) {
    $daysInMonth = (int)$cursor->format('t');
    $weeks = [];
    $week = array_fill(0, 7, null);

    $dayOfWeek = (int)$cursor->format('N');
    $day = 1;

    for ($i = $dayOfWeek - 1; $i < 7; $i++) {
        $date = $cursor->setDate($cursor->year, $cursor->month, $day);
        $week[$i] = $dayCell($date);
        $day++;
    }

    $weeks[] = $week;

    while ($day <= $daysInMonth) {
        $week = array_fill(0, 7, null);
        for ($i = 0; $i < 7 && $day <= $daysInMonth; $i++) {
            $date = $cursor->setDate($cursor->year, $cursor->month, $day);
            $week[$i] = $dayCell($date);
            $day++;
        }
        $weeks[] = $week;
    }

    while (count($weeks) < 6) {
        $weeks[] = array_fill(0, 7, null);
    }

    $months[] = [
        'label' => $monthNames[(int)$cursor->format('n')],
        'weeks' => $weeks,
    ];

    $cursor = $cursor->addMonths(1);
}

$courseLabel = sprintf(
    'CURS %d-%02d',
    $datainici->year,
    $datafi->year % 100
);

/* ============================
 * 5) VISTA
 * ============================ */
$this->assign('title', __('Calendari'));
echo $this->Html->css('calendar', ['block' => true]);
?>

<section class="annual-calendar">

    <aside class="annual-calendar__intro">
        <div class="annual-calendar__course">
            <?= h($courseLabel) ?>
        </div>

        <div class="annual-calendar__legend">

            <div class="annual-calendar__legend-item">
                <span class="annual-calendar__legend-swatch calendar-day--lectiu"></span>
                <span><?= __('Obert (lectiu)') ?></span>
            </div>

            <div class="annual-calendar__legend-item">
                <span class="annual-calendar__legend-swatch calendar-day--obert"></span>
                <span><?= __('Obert (no lectiu)') ?></span>
            </div>

            <div class="annual-calendar__legend-item">
                <span class="annual-calendar__legend-swatch calendar-day--festiu"></span>
                <span><?= __('Festiu') ?></span>
            </div>

            <div class="annual-calendar__legend-item">
                <span class="annual-calendar__legend-swatch calendar-day--closed"></span>
                <span><?= __('Tancat') ?></span>
            </div>

        </div>
    </aside>

    <div class="annual-calendar__months">
        <?php foreach ($months as $month): ?>
            <div class="month-card">
                <div class="month-card__header"><?= h($month['label']) ?></div>

                <table class="month-card__table">
                    <thead>
                        <tr>
                            <th>dl</th><th>dt</th><th>dc</th>
                            <th>dj</th><th>dv</th><th>ds</th><th>dg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($month['weeks'] as $week): ?>
                            <tr>
                                <?php foreach ($week as $day): ?>
                                    <?php if ($day === null): ?>
                                        <td class="month-card__empty"></td>
                                    <?php else: ?>
                                        <td class="<?= h($day['class']) ?>">
                                            <?= h($day['number']) ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="annual-calendar__actions">
        <?= $this->Html->link(
            __('Descarrega calendari anual (PDF)'),
            ['controller' => 'Calendar', 'action' => 'pdfAnnual'],
            ['class' => 'annual-calendar__action-btn']
        ) ?>

        <?= $this->Html->link(
            __('Descarrega calendari mensual (PDF)'),
            ['controller' => 'Calendar', 'action' => 'pdfMonthly'],
            ['class' => 'annual-calendar__action-btn']
        ) ?>
    </div>
</section>
