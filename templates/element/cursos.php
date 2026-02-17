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

    $formatter = new \IntlDateFormatter(
        'ca_ES',
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::NONE,
        $date->getTimezone()->getName(),
        \IntlDateFormatter::GREGORIAN,
        "EEEE, d 'de' MMMM 'de' yyyy"
    );

    $formatted = $formatter->format($date);

    return is_string($formatted) ? mb_strtolower($formatted) : '-';
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
    $rows = $connection->execute('SELECT id, nom, name FROM competenciestic')->fetchAll('assoc');
    foreach ($rows as $row) {
        $competencies[(int)$row['id']] = $row['name'] ?? $row['nom'] ?? '';
    }
}

$teachers = [];
if (in_array('teachers', $tables, true)) {
    try {
        $rows = $connection->execute('SELECT id, name FROM teachers')->fetchAll('assoc');
        foreach ($rows as $row) {
            $teachers[(int)$row['id']] = (string)$row['name'];
        }
    } catch (\Throwable $e) {
        $teachers = [];
    }
}

$colors = ['blaumari', 'blaucel', 'verd', 'rosa', 'lila', 'taronja', 'gris', 'ocre', 'grisclar'];
$subjectColors = [];
$nextColorIndex = 0;
?>

<div class="cursos-element">
    <?php foreach ($courses as $course): ?>
        <?php
        $paragraphs = preg_split('/\R{2,}/u', trim((string)$course->description)) ?: [];
        $paragraphItems = '';
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim(strip_tags((string)$paragraph));
            if ($paragraph === '') {
                continue;
            }
            $paragraphItems .= '<li>' . h($paragraph) . '</li>';
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
            $dayName = mb_strtolower((string)($horari->day->name ?? ''));
            $horariLines[] = sprintf(
                '%s: %s-%s',
                h($dayName),
                h($formatTime($horari->horainici)),
                h($formatTime($horari->horafinal))
            );
        }

        $competencia = '';
        if ($course->competenciatic_id !== null) {
            $competencia = $competencies[(int)$course->competenciatic_id] ?? '';
        }

        $teacherName = '-';
        if (property_exists($course, 'teacher') && $course->teacher !== null && isset($course->teacher->name)) {
            $teacherName = (string)$course->teacher->name;
        } elseif (isset($course->teacher_id) && isset($teachers[(int)$course->teacher_id])) {
            $teacherName = $teachers[(int)$course->teacher_id];
        }

        $nivell = (string)$course->level;
        $mecr = isset($course->mecr) && $course->mecr !== null && $course->mecr !== ''
            ? sprintf(' (%s MECR)', h((string)$course->mecr))
            : '';

        $materials = $getMaterialsForCourse($connection, $tables, (int)$course->id);
        $materialRows = '';
        $totalPrice = 0.0;

        foreach ($materials as $material) {
            $price = (float)($material['price'] ?? 0);
            $totalPrice += $price;

            $materialRows .= '<tr>'
                . '<td>' . h((string)($material['name'] ?? '')) . '</td>'
                . '<td>' . h((string)($material['description'] ?? '')) . '</td>'
                . '<td>' . h((string)($material['isbn'] ?? '')) . '</td>'
                . '<td class="preu-col">' . number_format($price, 2, ',', '.') . ' €</td>'
                . '</tr>';
        }

        $materialRows .= '<tr>'
            . '<td colspan="3"><strong>Preu total</strong></td>'
            . '<td class="preu-col preu-total">' . number_format($totalPrice, 2, ',', '.') . ' €</td>'
            . '</tr>';

        $content = '<ul class="cursos-llista">'
            . $paragraphItems
            . ($competencia !== '' ? '<li><strong>Competència:</strong> ' . h($competencia) . '</li>' : '')
            . '<li><strong>Data d\'inici:</strong> ' . h($formatDateCatalan($course->datainici)) . '</li>'
            . '<li><strong>Data d\'acabament:</strong> ' . h($formatDateCatalan($course->datafi)) . '</li>'
            . '<li><strong>Hores setmanals:</strong> ' . h(rtrim(rtrim(number_format($horesSetmanals, 2, ',', ''), '0'), ',')) . '</li>'
            . '<li><strong>Hores totals:</strong> ' . h((string)$course->horesanuals) . ' hores</li>'
            . '<li><strong>Aula:</strong> ' . h((string)($course->aula->name ?? '-')) . '</li>'
            . '<li><strong>Professor/a:</strong> ' . h($teacherName) . '</li>'
            . '<li><strong>Nivell:</strong> ' . h($nivell) . $mecr . '</li>'
            . '<li><strong>Horari:</strong></li>';

        foreach ($horariLines as $line) {
            $content .= '<li class="horari-linia">' . $line . '</li>';
        }

        $content .= '<li>Matrícula gratuïta.</li>'
            . '<li><strong>Preus:</strong></li>'
            . '</ul>'
            . '<table class="cursos-preus">'
            . '<tbody>'
            . $materialRows
            . '</tbody>'
            . '</table>';

        $subjectKey = (int)($course->subject_id ?? 0);
        if (!isset($subjectColors[$subjectKey])) {
            $subjectColors[$subjectKey] = $colors[$nextColorIndex % count($colors)];
            $nextColorIndex++;
        }

        echo $this->element('pestanya', [
            'titol' => (string)$course->name,
            'contingut' => $content,
            'color' => $subjectColors[$subjectKey],
            'extraClass' => 'cursos-pestanya',
        ]);
        ?>
    <?php endforeach; ?>
</div>

<style>
.cursos-element .cursos-pestanya .text .cursos-llista {
    margin-bottom: 1rem;
}

.cursos-element .horari-linia {
    text-align: center;
    list-style: none;
    margin-left: 0;
}

.cursos-element .cursos-preus {
    width: 80%;
    margin: 0 auto;
    border-collapse: collapse;
    border: none;
}

.cursos-element .cursos-preus td {
    border: none;
    padding: 0.25rem 0.5rem;
}

.cursos-element .cursos-preus .preu-col {
    text-align: right;
    white-space: nowrap;
}

.cursos-element .cursos-preus .preu-total {
    border-top: 1px solid #000;
}
</style>
