<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use setasign\Fpdi\Fpdi;

class HorarisController extends AppController
{
    public function pdf(): void
    {
        $data = $this->buildHorarisData();

        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AddFont('BebasNeue', '', 'BebasNeue.php');
        $pdf->AddFont('RobotoCondensed', '', 'RobotoCondensed-VariableFont_wght.php');
        $pdf->AddFont('RobotoCondensed', 'B', 'RobotoCondensed-Bold.php');

        $pdf->AddPage();
        $this->drawHeader($pdf, $data['yearLabel']);

        $x = 14.0;
        $y = 34.0;
        $wLeft = 56.0;
        $wMid = 88.0;
        $wRight = 36.0;
        $rowH = 7.2;

        foreach ($data['sections'] as $section) {
            $rows = $section['rows'];
            $rowsCount = count($rows);
            if ($rowsCount === 0) {
                continue;
            }

            $sectionHeight = 8.0 + ($rowsCount * $rowH) + 6.0;
            if ($y + $sectionHeight > 270) {
                $this->drawFooter($pdf);
                $pdf->AddPage();
                $this->drawHeader($pdf, $data['yearLabel']);
                $y = 34.0;
            }

            $rgb = $section['rgb'];

            $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->Rect($x + $wLeft + $wMid, $y, $wRight, 8.0, 'F');
            $pdf->SetDrawColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->SetLineWidth(0.55);
            $pdf->Rect($x + $wLeft + $wMid, $y, $wRight, 8.0, 'D');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('BebasNeue', '', 14);
            $pdf->SetXY($x + $wLeft + $wMid, $y + 1.1);
            $pdf->Cell($wRight, 6, 'AULA', 0, 0, 'C');

            $tableY = $y + 8.0;
            $tableH = $rowsCount * $rowH;

            $pdf->SetDrawColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->SetLineWidth(0.55);
            $pdf->Rect($x, $tableY, $wLeft + $wMid + $wRight, $tableH, 'D');

            for ($idx = 0; $idx < $rowsCount; $idx++) {
                $row = $rows[$idx];
                $rowY = $tableY + ($idx * $rowH);

                $span = 1;
                if (!empty($row['is_parent'])) {
                    for ($j = $idx + 1; $j < $rowsCount; $j++) {
                        $next = $rows[$j];
                        if (empty($next['is_parent']) || (string)($next['course'] ?? '') !== (string)($row['course'] ?? '')) {
                            break;
                        }
                        $span++;
                    }
                }

                $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
                $pdf->Rect($x, $rowY, $wLeft, $rowH * $span, 'F');

                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('BebasNeue', '', 11.8);
                $courseLineHeight = 4.5;
                $courseAvailableHeight = ($rowH * $span) - 1.4;
                $maxCourseLines = max(1, (int)floor($courseAvailableHeight / $courseLineHeight));
                $courseLines = $this->splitPdfTextLines($pdf, (string)($row['course'] ?? ''), $wLeft - 4);
                if (count($courseLines) > $maxCourseLines) {
                    $courseLines = array_slice($courseLines, 0, $maxCourseLines);
                    $lastLine = (string)array_pop($courseLines);
                    $courseLines[] = rtrim($lastLine, " \t\n\r\0\x0B-") . '…';
                }
                $courseTextHeight = count($courseLines) * $courseLineHeight;
                $courseTextY = $rowY + (($rowH * $span - $courseTextHeight) / 2);
                foreach ($courseLines as $lineIdx => $courseLine) {
                    $pdf->SetXY($x + 2, $courseTextY + ($lineIdx * $courseLineHeight));
                    $pdf->Cell($wLeft - 4, $courseLineHeight, $this->pdfText($courseLine), 0, 0, 'L');
                }

                for ($line = 0; $line < $span; $line++) {
                    $lineRow = $rows[$idx + $line];
                    $lineY = $tableY + (($idx + $line) * $rowH);
                    $pdf->SetTextColor(55, 55, 55);
                    $pdf->SetFont('RobotoCondensed', '', 10.4);
                    $pdf->SetXY($x + $wLeft + 2.6, $lineY + 1.05);
                    $pdf->Cell(54, 5.4, $this->pdfText((string)($lineRow['days'] ?? '')), 0, 0, 'L');

                    $pdf->SetXY($x + $wLeft + 54, $lineY + 1.05);
                    $pdf->Cell($wMid - 56, 5.4, $this->pdfText((string)($lineRow['hours'] ?? '')), 0, 0, 'L');

                    $pdf->SetXY($x + $wLeft + $wMid + 2, $lineY + 1.05);
                    $pdf->Cell($wRight - 4, 5.4, $this->pdfText((string)($lineRow['aula'] ?? '')), 0, 0, 'C');
                }

                $idx += ($span - 1);
            }

            $y = $tableY + $tableH + 6.0;
        }

        $this->drawFooter($pdf);
        $filename = sprintf('horaris-%s.pdf', strtolower(str_replace(' ', '-', $data['yearLabel'])));
        $content = $pdf->Output('S');
        $this->response = $this->response
            ->withType('pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withStringBody($content);
        $this->autoRender = false;
    }

    /**
     * @return array{yearLabel:string,sections:array<int,array{name:string,rgb:array<int,int>,rows:array<int,array{course:string,days:string,hours:string,aula:string}>}>}
     */
    private function buildHorarisData(): array
    {
        $Years = $this->fetchTable('Years');
        $year = $Years->find()->order(['Years.datafi' => 'DESC', 'Years.id' => 'DESC'])->first();
        if (!$year) {
            throw new NotFoundException(__('No year found for horaris.'));
        }

        $Courses = $this->fetchTable('Courses');
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

        $palette = [
            [229, 83, 129],  // rosa
            [112, 128, 144], // blaumari
            [142, 195, 195], // blaucel
            [174, 213, 129], // verd
            [254, 178, 14],  // taronja
            [171, 165, 186], // lila
            [168, 168, 168], // gris
        ];

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
            if ($subjectName === '') {
                $subjectName = (string)__('Altres');
            }

            $sectionKey = mb_strtolower($subjectName);
            if (!isset($sections[$sectionKey])) {
                $rgb = $palette[count($sections) % count($palette)];

                $sections[$sectionKey] = [
                    'name' => $subjectName,
                    'rgb' => $rgb,
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

            $level = trim((string)($course->level ?? ''));
            $tornName = trim((string)($course->torn->name ?? ''));
            $trioKey = mb_strtolower($sectionKey . '|' . $level . '|' . $tornName);
            $courseLabel = mb_strtoupper(trim((string)$course->name));
            $courseLabel = preg_replace('/\\s*-\\s*C\\d+$/u', '', $courseLabel) ?? $courseLabel;
            $courseLabel = preg_replace('/\\s*-\\s*\\d+$/u', '', $courseLabel) ?? $courseLabel;

            $courseNameNormalized = mb_strtolower((string)($course->name ?? ''));
            $looksLikeParentAccess = str_contains($courseNameNormalized, 'proves')
                && str_contains($courseNameNormalized, 'grau')
                && str_contains($courseNameNormalized, 'mitj');
            $isParentCourse = isset($parentCourseIds[(int)($course->id ?? 0)]) || $looksLikeParentAccess;

            if (!isset($sections[$sectionKey]['rows'][$trioKey])) {
                $sections[$sectionKey]['rows'][$trioKey] = [
                    'course' => $courseLabel,
                    'entries' => [],
                    'is_parent' => $isParentCourse,
                ];
            }

            $entries = [];
            if ($isParentCourse) {
                foreach ($horaris as $h) {
                    $dayName = mb_strtolower(trim((string)($h->day->name ?? '')));
                    $start = $this->formatHour($h->horainici ?? null);
                    $end = $this->formatHour($h->horafinal ?? null);
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
                    $start = $this->formatHour($h->horainici ?? null);
                    $end = $this->formatHour($h->horafinal ?? null);
                    if ($start !== '' && $end !== '') {
                        $range = $start . '-' . $end . 'h';
                        if (!in_array($range, $ranges, true)) {
                            $ranges[] = $range;
                        }
                    }
                }
                $entries[] = [
                    'days' => $this->joinDays($days),
                    'hours' => implode(' / ', $ranges),
                    'aula' => mb_strtolower((string)($course->aula->name ?? '')),
                ];
            }

            $merged = array_merge((array)$sections[$sectionKey]['rows'][$trioKey]['entries'], $entries);
            $dedup = [];
            foreach ($merged as $entry) {
                $k = (($entry['days'] ?? '') . '|' . ($entry['hours'] ?? '') . '|' . ($entry['aula'] ?? ''));
                $dedup[$k] = $entry;
            }
            $sections[$sectionKey]['rows'][$trioKey]['entries'] = array_values($dedup);
            $sections[$sectionKey]['rows'][$trioKey]['is_parent'] =
                (bool)$sections[$sectionKey]['rows'][$trioKey]['is_parent'] || $isParentCourse;
        }

        foreach ($sections as $key => $section) {
            $finalRows = [];
            foreach ((array)$section['rows'] as $row) {
                foreach ((array)$row['entries'] as $entry) {
                    $finalRows[] = [
                        'course' => (string)$row['course'],
                        'days' => (string)($entry['days'] ?? ''),
                        'hours' => (string)($entry['hours'] ?? ''),
                        'aula' => (string)($entry['aula'] ?? ''),
                        'is_parent' => (bool)($row['is_parent'] ?? false),
                    ];
                }
            }
            $sections[$key]['rows'] = $finalRows;
        }

        $yearLabel = sprintf(
            'HORARIS %d-%02d',
            (int)$year->datainici->format('Y'),
            ((int)$year->datafi->format('Y')) % 100
        );

        return [
            'yearLabel' => $yearLabel,
            'sections' => array_values($sections),
        ];
    }

    /**
     * @param array<int,string> $days
     */
    private function joinDays(array $days): string
    {
        $days = array_values(array_filter(array_map('trim', $days), static fn(string $d): bool => $d !== ''));
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
    }

    private function formatHour(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        $raw = (string)$value;
        if (preg_match('/^(\d{2}:\d{2})/', $raw, $m)) {
            return $m[1];
        }

        return substr($raw, 0, 5);
    }

    private function drawHeader(Fpdi $pdf, string $title): void
    {
        $logoPath = WWW_ROOT . 'img' . DS . 'logoGran.png';
        if (is_file($logoPath)) {
            $pdf->Image($logoPath, 14, 10, 40, 0);
        }

        $pdf->SetTextColor(70, 70, 70);
        $pdf->SetFont('RobotoCondensed', '', 12);
        $pdf->SetXY(0, 14);
        $pdf->Cell(210, 8, $this->pdfText($title), 0, 0, 'C');
    }

    private function drawFooter(Fpdi $pdf): void
    {
        $palette = [
            [132, 188, 192], [118, 136, 156], [171, 165, 186], [221, 79, 132],
            [164, 201, 117], [250, 177, 0], [168, 168, 168], [202, 178, 162],
        ];

        $x = 92.0;
        $y = 284.0;
        foreach ($palette as $color) {
            $pdf->SetFillColor($color[0], $color[1], $color[2]);
            $pdf->Rect($x, $y, 6.0, 6.0, 'F');
            $x += 6.8;
        }
    }

    private function pdfText(string $text): string
    {
        $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $decoded) ?: utf8_decode($decoded);
    }

    /**
     * @return array<int,string>
     */
    private function splitPdfTextLines(Fpdi $pdf, string $text, float $maxWidth): array
    {
        $words = preg_split('/\s+/u', trim($text)) ?: [];
        if ($words === []) {
            return [''];
        }

        $lines = [];
        $current = '';
        foreach ($words as $word) {
            $candidate = $current === '' ? $word : ($current . ' ' . $word);
            if ($current !== '' && $pdf->GetStringWidth($this->pdfText($candidate)) > $maxWidth) {
                $lines[] = $current;
                $current = $word;
                continue;
            }
            $current = $candidate;
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines === [] ? [''] : $lines;
    }
}
