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

$normalizeCourseName = static function (string $name): string {
    $label = mb_strtoupper(trim($name));

    if (preg_match('/^COMPETIC\\s*3\\s*-\\s*VESPRE\\s*-\\s*C\\d+$/u', $label)) {
        return 'COMPETIC 3 - VESPRE';
    }

    return $label;
};

$colorBySubject = [
    'preparació' => '#A4C975',
    'català' => '#84BCC0',
    'castellà' => '#ABA5BA',
    'anglès' => '#76889C',
    'competic' => '#DD4F84',
];
$fallbackPalette = ['#84BCC0', '#76889C', '#ABA5BA', '#DD4F84', '#A4C975', '#FAB100'];

$sections = [];
$parentCourseIds = [];
foreach ($courses as $maybeParent) {
    $courseId = (int)($maybeParent->id ?? 0);
    if ($courseId <= 0) {
        continue;
    }
    foreach ($courses as $candidateChild) {
        if ((int)($candidateChild->parentcourse_id ?? 0) === $courseId) {
            $parentCourseIds[$courseId] = true;
            break;
        }
    }
}

foreach ($courses as $course) {
    $subjectName = trim((string)($course->subject->name ?? __('Altres')));
    $sectionKey = mb_strtolower($subjectName !== '' ? $subjectName : (string)__('Altres'));

    if (!isset($sections[$sectionKey])) {
        $color = $fallbackPalette[count($sections) % count($fallbackPalette)];
        foreach ($colorBySubject as $needle => $value) {
            if (str_contains($sectionKey, $needle)) {
                $color = $value;
                break;
            }
        }

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

    $entries = [];
    $courseNameNormalized = mb_strtolower((string)($course->name ?? ''));
    $looksLikeParentAccess = str_contains($courseNameNormalized, 'proves')
        && str_contains($courseNameNormalized, 'grau')
        && str_contains($courseNameNormalized, 'mitj');
    $isParentCourse = isset($parentCourseIds[(int)($course->id ?? 0)]) || $looksLikeParentAccess;

    if ($isParentCourse) {
        foreach ($horaris as $h) {
            $dayName = mb_strtolower(trim((string)($h->day->name ?? '')));
            $start = $formatHour($h->horainici ?? null);
            $end = $formatHour($h->horafinal ?? null);

            if ($dayName === '' || $start === '' || $end === '') {
                continue;
            }

            $entries[] = [
                'days' => $dayName,
                'hours' => $start . '-' . $end . 'h',
                'aula' => mb_strtolower((string)($course->aula->name ?? '')),
            ];
        }
    } else {
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

        $entries[] = [
            'days' => $joinDays($days),
            'hours' => implode(' / ', $ranges),
            'aula' => mb_strtolower((string)($course->aula->name ?? '')),
        ];
    }

    $normalizedCourse = $normalizeCourseName((string)$course->name);
    $existingIdx = null;
    foreach ($sections[$sectionKey]['courses'] as $idx => $existing) {
        if (($existing['course'] ?? '') === $normalizedCourse) {
            $existingIdx = $idx;
            break;
        }
    }

    if ($existingIdx === null) {
        $sections[$sectionKey]['courses'][] = [
            'course' => $normalizedCourse,
            'entries' => $entries,
            'is_parent' => $isParentCourse,
        ];
    } else {
        $mergedEntries = array_merge(
            (array)$sections[$sectionKey]['courses'][$existingIdx]['entries'],
            $entries
        );
        $dedup = [];
        foreach ($mergedEntries as $entry) {
            $entryKey = (($entry['days'] ?? '') . '|' . ($entry['hours'] ?? '') . '|' . ($entry['aula'] ?? ''));
            $dedup[$entryKey] = $entry;
        }
        $sections[$sectionKey]['courses'][$existingIdx]['entries'] = array_values($dedup);
        $sections[$sectionKey]['courses'][$existingIdx]['is_parent'] =
            (bool)$sections[$sectionKey]['courses'][$existingIdx]['is_parent'] || $isParentCourse;
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
                    <?php
                    $entries = (array)$courseBlock['entries'];
                    $isParent = (bool)($courseBlock['is_parent'] ?? false);
                    $rowspan = max(1, count($entries));
                    ?>
                    <?php foreach ($entries as $idx => $entry): ?>
                        <tr>
                            <?php if (!$isParent || $idx === 0): ?>
                                <th
                                    scope="row"
                                    class="horaris-course-table__course"
                                    <?= $isParent && $rowspan > 1 ? 'rowspan="' . (int)$rowspan . '"' : '' ?>
                                ><?= h($courseBlock['course']) ?></th>
                            <?php endif; ?>
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
