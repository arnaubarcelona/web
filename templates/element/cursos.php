<?php
/**
 * Element: cursos
 */

declare(strict_types=1);

use Cake\Database\Connection;
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

$locator = TableRegistry::getTableLocator();
$yearsTable = $locator->get('Years');
$coursesTable = $locator->get('Courses');
$paginesTable = $locator->get('Pagines');
$connection = $coursesTable->getConnection();
$schema = $connection->getSchemaCollection();
$tables = $schema->listTables();
$courseColumns = in_array('courses', $tables, true) ? $schema->describe('courses')->columns() : [];

$formatDateCatalan = static function ($date): string {
    if (!$date) {
        return '-';
    }

    if (!$date instanceof \DateTimeInterface) {
        $date = FrozenDate::parse((string)$date);
    }

    if (!$date) {
        return '-';
    }

    $weekdayFormatter = new \IntlDateFormatter(
        'ca_ES',
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::NONE,
        $date->getTimezone()->getName(),
        \IntlDateFormatter::GREGORIAN,
        'EEEE'
    );

    $monthFormatter = new \IntlDateFormatter(
        'ca_ES',
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::NONE,
        $date->getTimezone()->getName(),
        \IntlDateFormatter::GREGORIAN,
        'LLLL'
    );

    $weekday = $weekdayFormatter->format($date);
    $month = $monthFormatter->format($date);

    if (!is_string($weekday) || !is_string($month)) {
        return '-';
    }

    return sprintf('%s, %d de %s de %d', mb_strtolower($weekday), (int)$date->format('j'), mb_strtolower($month), (int)$date->format('Y'));
};

$formatTime = static function ($time): string {
    if (!$time instanceof \DateTimeInterface) {
        $time = new \DateTime((string)$time);
    }

    return $time->format('H:i');
};

$timeToMinutes = static function ($time): int {
    if (!$time instanceof \DateTimeInterface) {
        $time = new \DateTime((string)$time);
    }

    return ((int)$time->format('H')) * 60 + (int)$time->format('i');
};

$getMaterialsForCourse = static function (Connection $conn, array $existingTables, int $courseId): array {
    if (!in_array('materials', $existingTables, true)) {
        return [];
    }

    $queries = [];

    if (in_array('courses_materials', $existingTables, true)) {
        $queries[] = 'SELECT m.name, m.description, m.isbn, m.price
            FROM materials m
            INNER JOIN courses_materials cm ON cm.material_id = m.id
            WHERE cm.course_id = :course_id';
    }

    if (in_array('course_materials', $existingTables, true)) {
        $queries[] = 'SELECT m.name, m.description, m.isbn, m.price
            FROM materials m
            INNER JOIN course_materials cm ON cm.material_id = m.id
            WHERE cm.course_id = :course_id';
    }

    if (in_array('materials_courses', $existingTables, true)) {
        $queries[] = 'SELECT m.name, m.description, m.isbn, m.price
            FROM materials m
            INNER JOIN materials_courses mc ON mc.material_id = m.id
            WHERE mc.course_id = :course_id';
    }

    $queries[] = 'SELECT m.name, m.description, m.isbn, m.price
        FROM materials m
        WHERE m.course_id = :course_id';

    foreach ($queries as $query) {
        try {
            $rows = $conn->execute($query, ['course_id' => $courseId])->fetchAll('assoc');
            if (!empty($rows)) {
                return $rows;
            }
        } catch (\Throwable $e) {
            continue;
        }
    }

    return [];
};

$getYearMaterialPrice = static function (Connection $conn, array $existingTables, array $materialColumns, int $yearId): float {
    if (!in_array('materials', $existingTables, true) || !in_array('year_id', $materialColumns, true)) {
        return 0.0;
    }

    try {
        $rows = $conn->execute(
            'SELECT price, name FROM materials WHERE year_id = :year_id',
            ['year_id' => $yearId]
        )->fetchAll('assoc');
    } catch (\Throwable $e) {
        return 0.0;
    }

    $total = 0.0;
    foreach ($rows as $row) {
        $name = mb_strtolower(trim((string)($row['name'] ?? '')));
        if ($name === 'material') {
            $total += (float)($row['price'] ?? 0);
        }
    }

    return $total;
};

$buildHorariAbreujat = static function (array $horaris, callable $formatTime): string {
    if (empty($horaris)) {
        return '';
    }

    $abbr = [
        'dilluns' => 'dl.',
        'dimarts' => 'dt.',
        'dimecres' => 'dc.',
        'dijous' => 'dj.',
        'divendres' => 'dv.',
        'dissabte' => 'ds.',
        'diumenge' => 'dg.',
    ];

    $items = [];
    foreach ($horaris as $h) {
        $dayName = mb_strtolower((string)($h->day->name ?? ''));
        $items[] = [
            'day_id' => (int)($h->day->id ?? $h->day_id ?? 0),
            'day' => $abbr[$dayName] ?? $dayName,
            'start' => $formatTime($h->horainici),
            'end' => $formatTime($h->horafinal),
        ];
    }

    usort($items, static function ($a, $b): int {
        if ($a['day_id'] === $b['day_id']) {
            return strcmp($a['start'], $b['start']);
        }

        return $a['day_id'] <=> $b['day_id'];
    });

    $firstStart = $items[0]['start'];
    $firstEnd = $items[0]['end'];
    $sameRange = true;
    $days = [];

    foreach ($items as $item) {
        $days[] = $item['day'];
        if ($item['start'] !== $firstStart || $item['end'] !== $firstEnd) {
            $sameRange = false;
        }
    }

    if ($sameRange) {
        return implode(' i ', $days) . ' de ' . $firstStart . ' a ' . $firstEnd;
    }

    $chunks = [];
    foreach ($items as $item) {
        $chunks[] = $item['day'] . ' de ' . $item['start'] . ' a ' . $item['end'];
    }

    return implode(', ', $chunks);
};

$areCoursesCompatible = static function (array $horarisA, array $horarisB, callable $timeToMinutes): bool {
    if (empty($horarisA) || empty($horarisB)) {
        return true;
    }

    foreach ($horarisA as $a) {
        $dayA = (int)($a->day_id ?? 0);
        $aStart = $timeToMinutes($a->horainici);
        $aEnd = $timeToMinutes($a->horafinal);

        foreach ($horarisB as $b) {
            if ($dayA !== (int)($b->day_id ?? 0)) {
                continue;
            }

            $bStart = $timeToMinutes($b->horainici);
            $bEnd = $timeToMinutes($b->horafinal);

            $overlap = min($aEnd, $bEnd) - max($aStart, $bStart);
            if ($overlap > 30) {
                return false;
            }
        }
    }

    return true;
};

$latestYear = $yearsTable->find()
    ->where(['Years.datainicipreinscripcio IS NOT' => null])
    ->order(['Years.datainicipreinscripcio' => 'DESC'])
    ->first();

if (!$latestYear) {
    echo '<p>No hi ha cursos disponibles.</p>';
    return;
}

$paginaMatricula = $paginesTable->find()
    ->select(['id'])
    ->where(['Pagines.title' => 'matricula'])
    ->first();
$matriculaUrl = $paginaMatricula ? $this->Url->build(['controller' => 'Pagines', 'action' => 'view', $paginaMatricula->id]) : '#';

$courses = $coursesTable->find()
    ->where([
        'Courses.year_id' => $latestYear->id,
        'Courses.propi' => 1,
        'Courses.microgrup' => 0,
    ])
    ->contain([
        'Aulas',
        'Subjects',
        'Horaris' => ['Days'],
    ])
    ->order(['Courses.name' => 'ASC'])
    ->all()
    ->toList();

$hasParentCourseId = in_array('parentcourse_id', $courseColumns, true);
$childParentIds = [];
if ($hasParentCourseId) {
    try {
        $rows = $connection->execute('SELECT DISTINCT parentcourse_id FROM courses WHERE parentcourse_id IS NOT NULL')->fetchAll('assoc');
        foreach ($rows as $row) {
            $childParentIds[(int)$row['parentcourse_id']] = true;
        }
    } catch (\Throwable $e) {
        $childParentIds = [];
    }
}

$competencies = [];
if (in_array('competenciestic', $tables, true)) {
    $competenciaSchema = $schema->describe('competenciestic');
    $competenciaColumns = $competenciaSchema->columns();
    $competenciaNameColumn = in_array('name', $competenciaColumns, true) ? 'name' : 'nom';

    $rows = $connection->execute(
        sprintf('SELECT id, %s FROM competenciestic', $competenciaNameColumn)
    )->fetchAll('assoc');

    foreach ($rows as $row) {
        $competencies[(int)$row['id']] = (string)($row[$competenciaNameColumn] ?? '');
    }
}

$materialColumns = [];
if (in_array('materials', $tables, true)) {
    $materialColumns = $schema->describe('materials')->columns();
}
$materialPriceByYear = $getYearMaterialPrice($connection, $tables, $materialColumns, (int)$latestYear->id);

$colors = ['blaumari', 'blaucel', 'verd', 'rosa', 'lila', 'taronja', 'gris', 'ocre', 'grisclar'];
$colorHexByName = [
    'rosa' => '#e55381',
    'blaucel' => '#8ec3c3',
    'lila' => '#b2abbe',
    'taronja' => '#feb20e',
    'blaumari' => '#708090',
    'verd' => '#aed581',
    'gris' => '#bfbfbf',
    'ocre' => '#d8baa9',
    'grisclar' => '#cfcfcf',
];
$subjectColors = [];
$subjectNames = [];
$nextColorIndex = 0;

foreach ($courses as $course) {
    $subjectKey = (int)($course->subject_id ?? 0);
    if (isset($subjectColors[$subjectKey])) {
        continue;
    }

    $subjectColors[$subjectKey] = $colors[$nextColorIndex % count($colors)];
    $subjectNames[$subjectKey] = (string)($course->subject->name ?? 'Altres');
    $nextColorIndex++;
}
?>


<div class="cursos-element" id="cursos-top">
    <div class="cursos-page is-active" data-page="subjects">
        <div class="cursos-buttons-grid">
            <?php
            $subjectsSorted = $subjectNames;
            asort($subjectsSorted, SORT_NATURAL | SORT_FLAG_CASE);
            foreach ($subjectsSorted as $subjectId => $subjectName):
                $subjectCourses = array_values(array_filter($courses, static function ($course) use ($subjectId): bool {
                    return (int)($course->subject_id ?? 0) === (int)$subjectId;
                }));
                $target = count($subjectCourses) === 1
                    ? 'course-' . (int)$subjectCourses[0]->id
                    : 'subject-' . (int)$subjectId;
            ?>
                <?php $subjectColorName = $subjectColors[(int)$subjectId] ?? 'grisclar'; ?>
                <button
                    type="button"
                    class="cursos-nav-button"
                    style="--btn-bg: <?= h($colorHexByName[$subjectColorName] ?? '#708090') ?>;"
                    data-target-page="<?= h($target) ?>">
                    <?= h($subjectName) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php foreach ($subjectsSorted as $subjectId => $subjectName): ?>
        <?php
        $subjectCourses = array_values(array_filter($courses, static function ($course) use ($subjectId): bool {
            return (int)($course->subject_id ?? 0) === (int)$subjectId;
        }));
        if (count($subjectCourses) <= 1) {
            continue;
        }
        ?>
        <div class="cursos-page" data-page="subject-<?= h((string)$subjectId) ?>">
            <div class="cursos-buttons-grid">
                <?php foreach ($subjectCourses as $subjectCourse): ?>
                    <?php
                    $subjectColorName = $subjectColors[(int)$subjectId] ?? 'grisclar';
                    $subjectColorHex = $colorHexByName[$subjectColorName] ?? '#708090';
                    ?>
                    <button
                        type="button"
                        class="cursos-nav-button"
                        style="--btn-bg: <?= h($subjectColorHex) ?>;"
                        data-target-page="course-<?= h((string)$subjectCourse->id) ?>">
                        <?= h((string)$subjectCourse->name) ?>
                    </button>
                <?php endforeach; ?>

                <button type="button" class="cursos-nav-button cursos-nav-button--back" data-target-page="subjects">
                    VEURE TOTS ELS ENSENYAMENTS
                </button>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($courses as $course): ?>
        <?php
        $courseAnchor = 'course-' . (int)$course->id;

        $paragraphs = preg_split('/\R{2,}/u', trim((string)$course->description)) ?: [];
        $descriptionItems = '';
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim(strip_tags((string)$paragraph));
            if ($paragraph === '') {
                continue;
            }
            $descriptionItems .= '<li>' . h($paragraph) . '</li>';
        }

        $horesSetmanals = 0.0;
        $horariLines = [];
        $horaris = (array)($course->horaris ?? []);

        usort($horaris, static function ($a, $b): int {
            $dayA = (int)($a->day->id ?? $a->day_id ?? 0);
            $dayB = (int)($b->day->id ?? $b->day_id ?? 0);

            if ($dayA === $dayB) {
                return strcmp((string)$a->horainici, (string)$b->horainici);
            }

            return $dayA <=> $dayB;
        });

        foreach ($horaris as $horari) {
            $horesSetmanals += (float)($horari->durada ?? 0);
            $horariLines[] = sprintf(
                '<li class="horari-linia"><strong>%s</strong> de %s a %s</li>',
                h(mb_strtolower((string)($horari->day->name ?? ''))),
                h($formatTime($horari->horainici)),
                h($formatTime($horari->horafinal))
            );
        }

        $compatibleItems = [];
        foreach ($courses as $otherCourse) {
            if ((int)$otherCourse->id === (int)$course->id) {
                continue;
            }
            if ((int)($otherCourse->subject_id ?? 0) === (int)($course->subject_id ?? 0)) {
                continue;
            }

            $otherHoraris = (array)($otherCourse->horaris ?? []);
            if (!$areCoursesCompatible($horaris, $otherHoraris, $timeToMinutes)) {
                continue;
            }

            $horariAbreujat = $buildHorariAbreujat($otherHoraris, $formatTime);
            $compatibleItems[] = '<li class="horari-linia cursos-compatible-item"><button type="button" class="cursos-link-course cursos-go-course" data-course-id="' . (int)$otherCourse->id . '">' . h((string)$otherCourse->name) . '</button>' . ($horariAbreujat !== '' ? '<span class="cursos-horari-abreujat">(' . h($horariAbreujat) . ')</span>' : '') . '</li>';
        }

        $competenciaItem = '';
        if ($course->competenciatic_id !== null) {
            $competencia = mb_strtolower((string)($competencies[(int)$course->competenciatic_id] ?? ''));
            if ($competencia !== '') {
                $competenciaItem = '<li>Es treballarà la competència de <strong>' . h($competencia) . '</strong>.</li>';
            }
        }

        $showLevel = !isset($childParentIds[(int)$course->id]);
        $nivell = (string)$course->level;
        $hasMecr = isset($course->mecr) && $course->mecr !== null && trim((string)$course->mecr) !== '';
        $levelItem = $showLevel
            ? '<li>És el <strong>nivell ' . h($nivell) . '</strong>' . ($hasMecr ? ' (' . h((string)$course->mecr) . ' del Marc Europeu Comú de Referència).' : '.') . '</li>'
            : '';

        $materials = $getMaterialsForCourse($connection, $tables, (int)$course->id);
        $materialLines = '';
        $courseMaterialsTotal = 0.0;

        foreach ($materials as $material) {
            $price = (float)($material['price'] ?? 0);
            $courseMaterialsTotal += $price;

            $name = mb_strtolower(trim((string)($material['name'] ?? '')));
            if ($name === '' || $name === 'material' || $name === 'material extra') {
                continue;
            }

            $description = trim((string)($material['description'] ?? ''));
            $descriptionText = $description !== '' ? ' es diu ' . h($description) : '';
            $isbn = trim((string)($material['isbn'] ?? ''));
            $isbnText = $isbn !== '' ? ' (ISBN: ' . h($isbn) . ')' : '';

            $materialLines .= '<li>El ' . h($name) . $descriptionText . $isbnText . ' i el podeu comprar al nostre centre al preu reduït de <strong>' . number_format($price, 2, ',', '.') . ' €</strong>.</li>';
        }

        $totalWithYearMaterial = $courseMaterialsTotal + $materialPriceByYear;
        $showTotal = abs($totalWithYearMaterial - $materialPriceByYear) > 0.0001;

        $compatibleListId = 'compatible-list-' . (int)$course->id;
        $compatibleList = empty($compatibleItems)
            ? '<li class="horari-linia cursos-compatible-item">Cap curs compatible.</li>'
            : implode('', $compatibleItems);

        $content = '<ul class="cursos-llista">'
            . $descriptionItems
            . $competenciaItem
            . $levelItem
            . '<li>El curs <strong>comença</strong> el ' . h($formatDateCatalan($course->datainici)) . ' i <strong>acaba</strong> el ' . h($formatDateCatalan($course->datafi)) . '.</li>'
            . '<li>Són <strong>' . h(rtrim(rtrim(number_format($horesSetmanals, 2, ',', ''), '0'), ',')) . ' hores</strong> a la setmana, <strong>' . h((string)$course->horesanuals) . ' hores</strong> en total.</li>'
            . '<li>Es fa a l\'<strong>' . h((string)($course->aula->name ?? '-')) . '</strong>, en aquest horari:</li>'
            . implode('', $horariLines)
            . '<li>La matrícula és <strong>gratuïta</strong>.</li>'
            . '<li>El preu del material és de <strong>' . number_format($materialPriceByYear, 2, ',', '.') . ' €</strong>.</li>'
            . $materialLines
            . ($showTotal ? '<li>En total són <strong>' . number_format($totalWithYearMaterial, 2, ',', '.') . ' €</strong>.</li>' : '')
            . '<li>Si t\'interessa aquest curs, <a href="' . h($matriculaUrl) . '">fes clic aquí</a>.</li>'
            . '<li>Els horaris d\'aquest curs són compatibles amb aquests <a href="#" class="cursos-compatible-toggle" data-target="' . h($compatibleListId) . '">altres cursos</a>.</li>'
            . '<li class="cursos-compatible-wrapper"><ul id="' . h($compatibleListId) . '" class="cursos-compatible-list">' . $compatibleList . '</ul></li>'
            . '</ul>';

        $subjectKey = (int)($course->subject_id ?? 0);
        $subjectColor = $subjectColors[$subjectKey] ?? $colors[0];
        $subjectColorHex = $colorHexByName[$subjectColor] ?? '#708090';
        $courseTitleHtml = '<span class="cursos-sticky-title-chip" style="--title-bg:' . h($subjectColorHex) . ';">' . h((string)$course->name) . '</span>';
        $subjectCoursesCount = count(array_filter($courses, static function ($item) use ($subjectKey): bool {
            return (int)($item->subject_id ?? 0) === $subjectKey;
        }));
        $backTarget = $subjectCoursesCount > 1 ? 'subject-' . $subjectKey : 'subjects';
        ?>
        <div class="cursos-page cursos-course-page" data-page="<?= h($courseAnchor) ?>" data-back-page="<?= h($backTarget) ?>">
            <?= $this->element('pestanya', [
                'titol' => (string)$course->name,
                'titolHtml' => $courseTitleHtml,
                'contingut' => $content,
                'color' => $subjectColor,
                'extraClass' => 'cursos-pestanya',
            ]) ?>

            <button type="button" class="cursos-nav-button cursos-nav-button--back cursos-back-course" data-target-page="<?= h($backTarget) ?>">
                VEURE ELS ALTRES GRUPS
            </button>
        </div>
    <?php endforeach; ?>
</div>

<style>
.cursos-page {
    display: none;
}

.cursos-page.is-active {
    display: block;
}

.cursos-page.is-active:not(.cursos-course-page) {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cursos-buttons-grid {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.cursos-nav-button {
    min-width: 25rem;
    border: 0;
    background: var(--btn-bg, #708090);
    color: #fff;
    padding: 0.9rem 1rem;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.4rem;
    cursor: pointer;
    text-align: center;
    transition: transform 160ms ease, box-shadow 180ms ease;
}

.cursos-nav-button--back {
    background: #111;
    text-align: center;
}

.cursos-back-course {
    margin-top: 1rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
    padding-left: 1.8rem;
    padding-right: 1.8rem;
    width: fit-content;
}

.cursos-nav-button:hover {
    transform: scale(1.03);
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.22);
}

.cursos-element .horari-linia {
    margin-left: 2rem !important;
}

.cursos-link-course {
    text-decoration: underline;
    font-weight: 700;
    color: inherit;
    background: transparent;
    border: 0;
    cursor: pointer;
    padding: 0;
    font: inherit;
    transition: transform 160ms ease, text-shadow 180ms ease;
}

.cursos-link-course:hover {
    transform: scale(1.03);
    text-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
}

.cursos-horari-abreujat {
    margin-left: 0.35rem;
}

.cursos-compatible-wrapper {
    list-style: none;
    padding-left: 0 !important;
    margin-bottom: 0;
}

.cursos-compatible-wrapper::before {
    content: none !important;
    display: none !important;
}

.cursos-compatible-list {
    display: none;
    margin: 0;
    padding-left: 0;
}

.cursos-compatible-list.is-open {
    display: block;
}

.cursos-course-page .pestanya .titol {
    position: sticky;
    top: 0;
    z-index: 8;
    background: #fff !important;
    padding: 0;
    margin-bottom: 0.5rem;
    width: 100%;
    max-width: 100%;
}

.cursos-sticky-title-chip {
    display: inline-block;
    background: var(--title-bg, #708090);
    color: #fff;
    padding: 0.75rem 1rem;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    line-height: 1;
}

.cursos-course-page .pestanya .text {
    margin-top: 0.5rem;
}

@media (prefers-reduced-motion: reduce) {
    .cursos-nav-button,
    .cursos-link-course {
        transition: none;
    }

    .cursos-nav-button:hover,
    .cursos-link-course:hover {
        transform: none;
        box-shadow: none;
        text-shadow: none;
    }
}

@media (max-width: 980px) {
    .cursos-element {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .cursos-nav-button {
        width: 100%;
        max-width: none;
    }

    .cursos-horari-abreujat {
        display: block;
        margin-left: 0;
    }


    .cursos-course-page .pestanya .titol {
        white-space: normal;
    }

    .cursos-sticky-title-chip {
        max-width: 100%;
        white-space: normal;
        overflow-wrap: anywhere;
    }

}
</style>

<script>
(function () {
    const pages = document.querySelectorAll('.cursos-page');
    let activePage = 'subjects';

    function renderPage(pageId) {
        activePage = pageId;
        pages.forEach((page) => {
            page.classList.toggle('is-active', page.dataset.page === pageId);
        });

        const top = document.getElementById('cursos-top');
        if (top) {
            top.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function showPage(pageId, pushState = true) {
        if (!pageId || pageId === activePage) {
            return;
        }

        renderPage(pageId);

        if (pushState) {
            window.history.pushState({ cursosPage: pageId }, '');
        }
    }

    function goBackOrFallback(fallbackPage) {
        if (window.history.state && window.history.state.cursosPage && window.history.length > 1) {
            window.history.back();
            return;
        }

        if (fallbackPage) {
            showPage(fallbackPage, false);
        }
    }

    document.querySelectorAll('.cursos-nav-button[data-target-page]').forEach((button) => {
        button.addEventListener('click', function () {
            const targetPage = this.dataset.targetPage;
            if (!targetPage) {
                return;
            }

            if (this.classList.contains('cursos-nav-button--back')) {
                goBackOrFallback(targetPage);
                return;
            }

            showPage(targetPage);
        });
    });

    document.querySelectorAll('.cursos-go-course').forEach((button) => {
        button.addEventListener('click', function () {
            const courseId = this.dataset.courseId;
            if (!courseId) {
                return;
            }
            showPage('course-' + courseId);
        });
    });

    document.querySelectorAll('.cursos-compatible-toggle').forEach((toggle) => {
        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            const targetId = this.dataset.target;
            if (!targetId) {
                return;
            }

            const list = document.getElementById(targetId);
            if (!list) {
                return;
            }

            list.classList.toggle('is-open');
        });
    });

    window.addEventListener('popstate', function (event) {
        const statePage = event.state && event.state.cursosPage ? event.state.cursosPage : 'subjects';
        renderPage(statePage);
    });

    renderPage('subjects');
    window.history.replaceState({ cursosPage: 'subjects' }, '');
})();
</script>
