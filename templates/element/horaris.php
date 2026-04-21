<?php
/**
 * Element: horaris
 *
 * Horaris de cursos propis no microgrup de l'any més recent.
 * Vista web: només taula d'horaris (sense logo, capçalera ni peu global).
 */

use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

$this->Html->css('horaris', ['block' => 'css']);

$Years = TableRegistry::getTableLocator()->get('Years');
$year = $Years->find()->order(['Years.datafi' => 'DESC', 'Years.id' => 'DESC'])->first();
if (!$year) {
    throw new NotFoundException(__('No year found for horaris.'));
}

$Courses = TableRegistry::getTableLocator()->get('Courses');
$courses = $Courses->find()
    ->where([
        'Courses.year_id' => (int)$year->id,
        'Courses.microgrup' => 0,
        'Courses.propi' => 1,
    ])
    ->contain(['Subjects', 'Aulas', 'Horaris' => ['Days']])
    ->order(['Subjects.name' => 'ASC', 'Courses.name' => 'ASC'])
    ->all()
    ->toList();

$dayOrder = [
    'dilluns' => 1,
    'dimarts' => 2,
    'dimecres' => 3,
    'dijous' => 4,
    'divendres' => 5,
    'dissabte' => 6,
    'diumenge' => 7,
];

$joinDays = static function (array $days): string {
    $days = array_values(array_filter(array_map(static fn($d) => trim((string)$d), $days), static fn(string $d): bool => $d !== ''));
    $count = count($days);
    if ($count === 0) {
        return '';
    }
    if ($count === 1) {
        return $days[0];
    }
    if ($count === 2) {
        return $days[0] . ' i ' . $days[1];
    }

    $last = array_pop($days);
    return implode(', ', $days) . ' i ' . $last;
};

$formatHour = static function ($value): string {
    if (empty($value)) {
        return '';
    }
    $raw = (string)$value;
    if (preg_match('/^(\d{2}:\d{2})/', $raw, $m)) {
        return $m[1];
    }
    return substr($raw, 0, 5);
};

$colorBySubject = [
    'preparació' => '#A4C975',
    'català' => '#84BCC0',
    'castellà' => '#ABA5BA',
    'anglès' => '#76889C',
    'competic' => '#DD4F84',
];

$sections = [];
foreach ($courses as $course) {
    $subjectName = trim((string)($course->subject->name ?? __('Altres')));
    $sectionKey = mb_strtolower($subjectName !== '' ? $subjectName : (string)__('Altres'));

    if (!isset($sections[$sectionKey])) {
        $color = '#84BCC0';
        foreach ($colorBySubject as $needle => $value) {
            if (str_contains($sectionKey, $needle)) {
                $color = $value;
                break;
            }
        }

        $sections[$sectionKey] = [
            'name' => $subjectName,
            'color' => $color,
            'rows' => [],
        ];
    }

    $horaris = (array)($course->horaris ?? []);
    usort($horaris, static function ($a, $b) use ($dayOrder): int {
        $nameA = mb_strtolower((string)($a->day->name ?? ''));
        $nameB = mb_strtolower((string)($b->day->name ?? ''));
        $oa = $dayOrder[$nameA] ?? 99;
        $ob = $dayOrder[$nameB] ?? 99;
        if ($oa !== $ob) {
            return $oa <=> $ob;
        }
        return strcmp((string)($a->horainici ?? ''), (string)($b->horainici ?? ''));
    });

    $days = [];
    $ranges = [];
    foreach ($horaris as $h) {
        $dayName = mb_strtolower(trim((string)($h->day->name ?? '')));
        if ($dayName !== '' && !in_array($dayName, $days, true)) {
            $days[] = $dayName;
        }

        $start = $formatHour($h->horainici ?? null);
        $end = $formatHour($h->horafinal ?? null);
        if ($start !== '' && $end !== '') {
            $range = $start . '-' . $end . 'h';
            if (!in_array($range, $ranges, true)) {
                $ranges[] = $range;
            }
        }
    }

    $sections[$sectionKey]['rows'][] = [
        'course' => mb_strtoupper((string)$course->name),
        'days' => $joinDays($days),
        'hours' => implode(' / ', $ranges),
        'aula' => mb_strtolower((string)($course->aula->name ?? '')),
    ];
}

$yearLabel = sprintf('Horaris %d-%02d', (int)$year->datainici->format('Y'), ((int)$year->datafi->format('Y')) % 100);
?>

<section class="horaris-board" aria-label="<?= h($yearLabel) ?>">
    <div class="horaris-board__actions">
        <?= $this->Html->link(
            __('Descarrega horaris (PDF)'),
            ['controller' => 'Horaris', 'action' => 'pdf'],
            ['class' => 'horaris-board__download-btn']
        ) ?>
    </div>

    <?php foreach (array_values($sections) as $section): ?>
        <div class="horaris-section" style="--section-color: <?= h($section['color']) ?>;">
            <div class="horaris-section__header">AULA</div>

            <table class="horaris-section__table" role="table">
                <tbody>
                <?php foreach ($section['rows'] as $row): ?>
                    <tr>
                        <th scope="row" class="horaris-section__course"><?= h($row['course']) ?></th>
                        <td class="horaris-section__days"><?= h($row['days']) ?></td>
                        <td class="horaris-section__hours"><?= h($row['hours']) ?></td>
                        <td class="horaris-section__aula"><?= h($row['aula']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</section>
