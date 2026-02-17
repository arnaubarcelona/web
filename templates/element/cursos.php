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
$connection = $coursesTable->getConnection();
$schema = $connection->getSchemaCollection();
$tables = $schema->listTables();

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

$latestYear = $yearsTable->find()
    ->where(['Years.datainicipreinscripcio IS NOT' => null])
    ->order(['Years.datainicipreinscripcio' => 'DESC'])
    ->first();

if (!$latestYear) {
    echo '<p>No hi ha cursos disponibles.</p>';
    return;
}

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
    ->all();

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
$subjectColors = [];
$subjectNames = [];
$nextColorIndex = 0;
?>

<div class="cursos-element">
    <h1>CURSOS</h1>

    <div class="cursos-subject-buttons">
        <?php foreach ($courses as $course): ?>
            <?php
            $subjectKey = (int)($course->subject_id ?? 0);
            if (isset($subjectColors[$subjectKey])) {
                continue;
            }

            $subjectColors[$subjectKey] = $colors[$nextColorIndex % count($colors)];
            $subjectNames[$subjectKey] = (string)($course->subject->name ?? 'Altres');
            $nextColorIndex++;
            ?>
            <button
                type="button"
                class="cursos-subject-button pestanya-<?= h($subjectColors[$subjectKey]) ?>"
                data-subject="<?= h((string)$subjectKey) ?>"
            >
                <?= h($subjectNames[$subjectKey]) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="cursos-tabs">
        <?php foreach ($courses as $course): ?>
            <?php
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
                $dayA = (int)($a->day_id ?? 0);
                $dayB = (int)($b->day_id ?? 0);

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

            $competenciaItem = '';
            if ($course->competenciatic_id !== null) {
                $competencia = mb_strtolower((string)($competencies[(int)$course->competenciatic_id] ?? ''));
                if ($competencia !== '') {
                    $competenciaItem = '<li>Es treballarà la competència de <strong>' . h($competencia) . '</strong>.</li>';
                }
            }

            $nivell = (string)$course->level;
            $hasMecr = isset($course->mecr) && $course->mecr !== null && $course->mecr !== '';

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

                $materialLines .= '<li>El ' . h($name) . $descriptionText . $isbnText . ' val <strong>' . number_format($price, 2, ',', '.') . ' €</strong>.</li>';
            }

            $totalWithYearMaterial = $courseMaterialsTotal + $materialPriceByYear;

            $content = '<ul class="cursos-llista">'
                . $descriptionItems
                . $competenciaItem
                . '<li>És el <strong>nivell ' . h($nivell) . '</strong>' . ($hasMecr ? ' (equivalent al nivell ' . h((string)$course->mecr) . ' del Marc Europeu Comú de Referència).' : '.') . '</li>'
                . '<li>El curs <strong>comença</strong> el ' . h($formatDateCatalan($course->datainici)) . ' i <strong>acaba</strong> el ' . h($formatDateCatalan($course->datafi)) . '.</li>'
                . '<li>Són <strong>' . h(rtrim(rtrim(number_format($horesSetmanals, 2, ',', ''), '0'), ',')) . ' hores</strong> a la setmana, <strong>' . h((string)$course->horesanuals) . ' hores</strong> en total.</li>'
                . '<li>Es fa a l\'<strong>' . h((string)($course->aula->name ?? '-')) . '</strong>, en aquest horari:</li>'
                . implode('', $horariLines)
                . '<li>La matrícula és <strong>gratuïta</strong>.</li>'
                . '<li>El preu del material és de <strong>' . number_format($materialPriceByYear, 2, ',', '.') . ' €</strong>.</li>'
                . $materialLines
                . ($totalWithYearMaterial > $courseMaterialsTotal
                    ? '<li>En total són <strong>' . number_format($totalWithYearMaterial, 2, ',', '.') . ' €</strong>.</li>'
                    : '')
                . '</ul>';

            $subjectKey = (int)($course->subject_id ?? 0);
            $subjectColor = $subjectColors[$subjectKey] ?? $colors[0];
            ?>
            <div class="cursos-tab-item" data-subject="<?= h((string)$subjectKey) ?>">
                <?= $this->element('pestanya', [
                    'titol' => (string)$course->name,
                    'contingut' => $content,
                    'color' => $subjectColor,
                    'extraClass' => 'cursos-pestanya',
                ]) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.cursos-element h1 {
    text-align: center;
    margin-bottom: 1rem;
}

.cursos-subject-buttons {
    text-align: center;
    margin-bottom: 1.5rem;
}

.cursos-subject-button {
    border: 0;
    color: #fff;
    cursor: pointer;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.5rem;
    margin: 0.3rem;
    padding: 0.45rem 1rem;
}

.cursos-subject-button.pestanya-blaumari { background-color: #708090; }
.cursos-subject-button.pestanya-blaucel { background-color: #8ec3c3; }
.cursos-subject-button.pestanya-verd { background-color: #aed581; }
.cursos-subject-button.pestanya-rosa { background-color: #e55381; }
.cursos-subject-button.pestanya-lila { background-color: #b2abbe; }
.cursos-subject-button.pestanya-taronja { background-color: #feb20e; }
.cursos-subject-button.pestanya-gris { background-color: #bfbfbf; }
.cursos-subject-button.pestanya-ocre { background-color: #d8baa9; }
.cursos-subject-button.pestanya-grisclar { background-color: #cfcfcf; }

.cursos-tab-item {
    display: none;
}

.cursos-element .horari-linia {
    margin-left: 2rem !important;
}
</style>

<script>
(function () {
    const buttons = document.querySelectorAll('.cursos-subject-button');
    const tabs = document.querySelectorAll('.cursos-tab-item');
    let activeSubject = null;

    const showSubject = function (subjectId) {
        tabs.forEach((tab) => {
            tab.style.display = tab.dataset.subject === subjectId ? '' : 'none';
        });
    };

    const hideAll = function () {
        tabs.forEach((tab) => {
            tab.style.display = 'none';
        });
    };

    hideAll();

    buttons.forEach((button) => {
        button.addEventListener('click', function () {
            const subject = this.dataset.subject;

            if (activeSubject === subject) {
                activeSubject = null;
                hideAll();
                return;
            }

            activeSubject = subject;
            showSubject(subject);
        });
    });
})();
</script>
