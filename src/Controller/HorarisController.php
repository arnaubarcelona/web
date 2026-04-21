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
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('BebasNeue', '', 14);
            $pdf->SetXY($x + $wLeft + $wMid, $y + 1.1);
            $pdf->Cell($wRight, 6, 'AULA', 0, 0, 'C');

            $tableY = $y + 8.0;
            $tableH = $rowsCount * $rowH;

            $pdf->SetDrawColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->SetLineWidth(0.55);
            $pdf->Rect($x, $tableY, $wLeft + $wMid + $wRight, $tableH, 'D');

            foreach ($rows as $idx => $row) {
                $rowY = $tableY + ($idx * $rowH);

                $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
                $pdf->Rect($x, $rowY, $wLeft, $rowH, 'F');

                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('BebasNeue', '', 12.5);
                $pdf->SetXY($x + 2, $rowY + 1.15);
                $pdf->Cell($wLeft - 4, 5.5, $this->pdfText($row['course']), 0, 0, 'C');

                $pdf->SetTextColor(55, 55, 55);
                $pdf->SetFont('RobotoCondensed', '', 10.4);
                $pdf->SetXY($x + $wLeft + 2.6, $rowY + 1.05);
                $pdf->Cell(54, 5.4, $this->pdfText($row['days']), 0, 0, 'L');

                $pdf->SetXY($x + $wLeft + 54, $rowY + 1.05);
                $pdf->Cell($wMid - 56, 5.4, $this->pdfText($row['hours']), 0, 0, 'L');

                $pdf->SetXY($x + $wLeft + $wMid + 2, $rowY + 1.05);
                $pdf->Cell($wRight - 4, 5.4, $this->pdfText($row['aula']), 0, 0, 'C');

                if ($idx < $rowsCount - 1) {
                    $pdf->SetDrawColor(215, 215, 215);
                    $pdf->SetLineWidth(0.2);
                    $pdf->Line($x, $rowY + $rowH, $x + $wLeft + $wMid + $wRight, $rowY + $rowH);
                }
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

        $colorBySubject = [
            'preparació' => [164, 201, 117],
            'català' => [132, 188, 192],
            'castellà' => [171, 165, 186],
            'anglès' => [118, 136, 156],
            'competic' => [221, 79, 132],
        ];

        $sections = [];
        foreach ($courses as $course) {
            $subjectName = trim((string)($course->subject->name ?? __('Altres')));
            if ($subjectName === '') {
                $subjectName = (string)__('Altres');
            }

            $sectionKey = mb_strtolower($subjectName);
            if (!isset($sections[$sectionKey])) {
                $rgb = [132, 188, 192];
                foreach ($colorBySubject as $needle => $color) {
                    if (str_contains($sectionKey, $needle)) {
                        $rgb = $color;
                        break;
                    }
                }

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

            $daysText = $this->joinDays($days);
            $hoursText = implode(' / ', $ranges);

            $sections[$sectionKey]['rows'][] = [
                'course' => mb_strtoupper((string)$course->name),
                'days' => $daysText,
                'hours' => $hoursText,
                'aula' => mb_strtolower((string)($course->aula->name ?? '')),
            ];
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
            $pdf->Image($logoPath, 14, 10, 40, 20);
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
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
