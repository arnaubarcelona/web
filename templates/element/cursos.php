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

    $formatted = sprintf('%s, %d de %s de %d', mb_strtolower($weekday), (int)$date->format('j'), mb_strtolower($month), (int)$date->format('Y'));

    return $formatted;
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
            $paragraphItems .= '<p>' . h($paragraph) . '</p>';
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
            $horariLines[] = [
                'day' => $dayName,
                'start' => $formatTime($horari->horainici),
                'end' => $formatTime($horari->horafinal),
            ];
        }

        $competencia = '';
        if ($course->competenciatic_id !== null) {
            $competencia = mb_strtolower((string)($competencies[(int)$course->competenciatic_id] ?? ''));
        }

        $nivell = (string)$course->level;
        $hasMecr = isset($course->mecr) && $course->mecr !== null && $course->mecr !== '';

        $materials = $getMaterialsForCourse($connection, $tables, (int)$course->id);
        $materialLines = '';
        $totalPrice = 0.0;
        $preuMaterial = 0.0;

        $isGenericMaterial = static function (string $name): bool {
            $normalized = mb_strtolower(trim($name));

            return $normalized === 'material' || $normalized === 'material extra';
        };

        foreach ($materials as $material) {
            $price = (float)($material['price'] ?? 0);
            $totalPrice += $price;

            $name = trim((string)($material['name'] ?? ''));
            if (mb_strtolower($name) === 'material') {
                $preuMaterial += $price;
            }

            if ($name === '' || $isGenericMaterial($name)) {
                continue;
            }

            $isbn = trim((string)($material['isbn'] ?? ''));
            $isbnText = $isbn !== '' ? ' (ISBN: ' . h($isbn) . ')' : '';
            $materialLines .= '<p>El ' . h(mb_strtolower($name)) . $isbnText . ' val <strong>' . number_format($price, 2, ',', '.') . ' €</strong>.</p>';
        }

        $content = '<div class="cursos-contingut">'
            . $paragraphItems
            . ($competencia !== '' ? '<p>Tractarà la competència de <strong>' . h($competencia) . '</strong>.</p>' : '')
            . '<p>És el <strong>nivell ' . h($nivell) . '</strong>' . ($hasMecr ? ' (equivalent al nivell ' . h((string)$course->mecr) . ' del Marc Europeu Comú de Referència).' : '.') . '</p>'
            . '<p>El curs <strong>comença el ' . h($formatDateCatalan($course->datainici)) . ' i </strong>acaba el ' . h($formatDateCatalan($course->datafi)) . '.</p>'
            . '<p>Són <strong>' . h(rtrim(rtrim(number_format($horesSetmanals, 2, ',', ''), '0'), ',')) . ' hores</strong> a la setmana, <strong>' . h((string)$course->horesanuals) . ' hores</strong> en total.</p>'
            . '<p>Es fa a l\'<strong>' . h((string)($course->aula->name ?? '-')) . '</strong>, en aquest horari:</p>';

        foreach ($horariLines as $line) {
            $content .= '<p class="horari-linia"><strong>' . h((string)$line['day']) . '</strong> de ' . h((string)$line['start']) . ' a ' . h((string)$line['end']) . '</p>';
        }

        $content .= '<p>La matrícula és <strong>gratuïta</strong>.</p>'
            . '<p>El preu del material és de <strong>' . number_format($preuMaterial, 2, ',', '.') . ' €</strong>.</p>'
            . $materialLines
            . '<p>En total són <strong>' . number_format($totalPrice, 2, ',', '.') . ' €</strong>.</p>'
            . '</div>';

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
.cursos-element .cursos-pestanya .text .cursos-contingut p {
    margin: 0 0 0.65rem;
}

.cursos-element .horari-linia {
    margin-left: 2rem !important;
}
</style>
