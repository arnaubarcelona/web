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
    ->contain(['Subjects', 'Torns', 'Aulas', 'Horaris' => ['Days']])
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

$palette = ['#E55381', '#708090', '#8EC3C3', '#AED581', '#FEB20E', '#ABA5BA', '#A8A8A8'];

$sections = [];

foreach ($courses as $course) {
    $subjectName = trim((string)($course->subject->name ?? __('Altres')));
    $sectionKey = mb_strtolower($subjectName !== '' ? $subjectName : (string)__('Altres'));

    if (!isset($sections[$sectionKey])) {
        $color = $palette[count($sections) % count($palette)];

        $sections[$sectionKey] = [
            'name' => $subjectName,
            'color' => $color,
            'courses' => [],
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

    $level = trim((string)($course->level ?? ''));
    $tornName = trim((string)($course->torn->name ?? ''));
    $trioKey = mb_strtolower($sectionKey . '|' . $level . '|' . $tornName);
    $label = mb_strtoupper(trim($subjectName . ' ' . $level . ' - ' . $tornName));

    $existingIdx = null;
    foreach ($sections[$sectionKey]['courses'] as $idx => $existing) {
        if (($existing['trio_key'] ?? '') === $trioKey) {
            $existingIdx = $idx;
            break;
        }
    }

    if ($existingIdx === null) {
        $sections[$sectionKey]['courses'][] = [
            'trio_key' => $trioKey,
            'course' => $label,
            'days' => [],
            'ranges' => [],
            'aulas' => [],
        ];
        $existingIdx = count($sections[$sectionKey]['courses']) - 1;
    }

    foreach ($horaris as $h) {
        $dayName = mb_strtolower(trim((string)($h->day->name ?? '')));
        if ($dayName !== '' && !in_array($dayName, $sections[$sectionKey]['courses'][$existingIdx]['days'], true)) {
            $sections[$sectionKey]['courses'][$existingIdx]['days'][] = $dayName;
        }

        $start = $formatHour($h->horainici ?? null);
        $end = $formatHour($h->horafinal ?? null);
        if ($start !== '' && $end !== '') {
            $range = $start . '-' . $end . 'h';
            if (!in_array($range, $sections[$sectionKey]['courses'][$existingIdx]['ranges'], true)) {
                $sections[$sectionKey]['courses'][$existingIdx]['ranges'][] = $range;
            }
        }
    }

    $aulaName = mb_strtolower((string)($course->aula->name ?? ''));
    if ($aulaName !== '' && !in_array($aulaName, $sections[$sectionKey]['courses'][$existingIdx]['aulas'], true)) {
        $sections[$sectionKey]['courses'][$existingIdx]['aulas'][] = $aulaName;
    }
}

foreach ($sections as $sectionKey => $section) {
    foreach ($section['courses'] as $idx => $courseRow) {
        $sections[$sectionKey]['courses'][$idx]['entries'] = [[
            'days' => $joinDays((array)$courseRow['days']),
            'hours' => implode(' / ', (array)$courseRow['ranges']),
            'aula' => implode(' / ', (array)$courseRow['aulas']),
        ]];
    }
}

$yearLabel = sprintf('Horaris %d-%02d', (int)$year->datainici->format('Y'), ((int)$year->datafi->format('Y')) % 100);
?>

<section class="horaris-board" aria-label="<?= h($yearLabel) ?>">
    <?php foreach (array_values($sections) as $section): ?>
        <div class="horaris-section" style="--section-color: <?= h($section['color']) ?>;" aria-label="<?= h($section['name']) ?>">
            <table class="horaris-course-table" role="table">
                <thead>
                    <tr>
                        <th class="horaris-course-table__spacer"></th>
                        <th class="horaris-course-table__spacer"></th>
                        <th class="horaris-course-table__spacer"></th>
                        <th class="horaris-course-table__aula-head">AULA</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($section['courses'] as $courseBlock): ?>
                    <?php foreach ((array)$courseBlock['entries'] as $entry): ?>
                        <tr>
                            <th scope="row" class="horaris-course-table__course"><?= h($courseBlock['course']) ?></th>
                            <td class="horaris-course-table__days"><?= h($entry['days']) ?></td>
                            <td class="horaris-course-table__hours"><?= h($entry['hours']) ?></td>
                            <td class="horaris-course-table__aula"><?= h($entry['aula']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

    <div class="horaris-board__actions">
        <?= $this->Html->link(
            __('Descarrega horaris (PDF)'),
            ['controller' => 'Horaris', 'action' => 'pdf'],
            ['class' => 'horaris-board__download-btn']
        ) ?>
    </div>
</section>
