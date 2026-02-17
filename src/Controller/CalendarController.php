<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenDate;
use setasign\Fpdi\Fpdi;

class CalendarController extends AppController
{
    public function index(): void
    {
        $this->set($this->calendarData());
    }

    public function pdfAnnual(): void
    {
        $calendar = $this->calendarData();
        $pdf = new Fpdi('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $this->renderPdfHeader($pdf, $calendar['courseLabel']);
        $this->renderAnnualMonths($pdf, $calendar['months']);

        $this->respondPdfDownload($pdf, sprintf('calendari-anual-%s.pdf', strtolower(str_replace(' ', '-', $calendar['courseLabel']))));
    }

    public function pdfMonthly(): void
    {
        $calendar = $this->calendarData();
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);

        foreach ($calendar['months'] as $month) {
            $pdf->AddPage();
            $this->renderPdfHeader($pdf, sprintf('%s · %s', $calendar['courseLabel'], $month['label']));
            $this->renderSingleMonth($pdf, $month, 20, 35, 170, 240);
        }

        $this->respondPdfDownload($pdf, sprintf('calendari-mensual-%s.pdf', strtolower(str_replace(' ', '-', $calendar['courseLabel']))));
    }

    private function respondPdfDownload(Fpdi $pdf, string $filename): void
    {
        $content = $pdf->Output('S');
        $this->response = $this->response
            ->withType('pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withStringBody($content);
        $this->autoRender = false;
    }

    private function renderPdfHeader(Fpdi $pdf, string $title): void
    {
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(68, 68, 68);
        $pdf->SetXY(12, 10);
        $pdf->Cell(0, 10, $this->pdfText($title), 0, 1, 'L');

        $this->drawLegend($pdf, 12, 22);
    }

    private function drawLegend(Fpdi $pdf, float $x, float $y): void
    {
        $legend = [
            ['label' => 'Obert (lectiu)', 'color' => [255, 255, 255]],
            ['label' => 'Obert (no lectiu)', 'color' => [252, 229, 205]],
            ['label' => 'Festiu', 'color' => [244, 204, 204]],
            ['label' => 'Tancat', 'color' => [244, 244, 246]],
        ];

        $pdf->SetFont('Arial', '', 9);
        $itemX = $x;

        foreach ($legend as $item) {
            $pdf->SetDrawColor(178, 171, 191);
            $pdf->SetFillColor($item['color'][0], $item['color'][1], $item['color'][2]);
            $pdf->Rect($itemX, $y + 1, 4, 4, 'DF');
            $pdf->SetXY($itemX + 6, $y);
            $pdf->Cell(35, 6, $this->pdfText((string)$item['label']), 0, 0, 'L');
            $itemX += 46;
        }
    }

    /**
     * @param array<int, array<string, mixed>> $months
     */
    private function renderAnnualMonths(Fpdi $pdf, array $months): void
    {
        $startX = 12;
        $startY = 32;
        $cols = 4;
        $gapX = 4;
        $gapY = 6;
        $monthWidth = 67;
        $monthHeight = 58;

        foreach ($months as $idx => $month) {
            $col = $idx % $cols;
            $row = intdiv($idx, $cols);

            $x = $startX + $col * ($monthWidth + $gapX);
            $y = $startY + $row * ($monthHeight + $gapY);

            $this->renderSingleMonth($pdf, $month, $x, $y, $monthWidth, $monthHeight);
        }
    }

    /**
     * @param array<string, mixed> $month
     */
    private function renderSingleMonth(Fpdi $pdf, array $month, float $x, float $y, float $width, float $height): void
    {
        $headerHeight = 7;
        $weekHeaderHeight = 6;
        $days = ['dl', 'dt', 'dc', 'dj', 'dv', 'ds', 'dg'];

        $pdf->SetDrawColor(178, 171, 191);
        $pdf->SetLineWidth(0.25);

        $pdf->SetFillColor(178, 171, 191);
        $pdf->Rect($x, $y, $width, $headerHeight, 'DF');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY($x, $y + 1.5);
        $pdf->Cell($width, 4, $this->pdfText((string)$month['label']), 0, 0, 'C');

        $tableY = $y + $headerHeight;
        $cellWidth = $width / 7;
        $rows = 7;
        $cellHeight = ($height - $headerHeight) / $rows;

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetFillColor(228, 223, 238);
        $pdf->SetTextColor(67, 67, 67);
        foreach ($days as $i => $day) {
            $cellX = $x + ($i * $cellWidth);
            $pdf->Rect($cellX, $tableY, $cellWidth, $weekHeaderHeight, 'DF');
            $pdf->SetXY($cellX, $tableY + 1.5);
            $pdf->Cell($cellWidth, 3, $day, 0, 0, 'C');
        }

        $weeks = $month['weeks'];
        for ($row = 0; $row < 6; $row++) {
            $week = $weeks[$row] ?? array_fill(0, 7, null);
            for ($col = 0; $col < 7; $col++) {
                $day = $week[$col] ?? null;
                $cellX = $x + ($col * $cellWidth);
                $cellY = $tableY + $weekHeaderHeight + ($row * $cellHeight);

                if ($day === null) {
                    $pdf->SetFillColor(255, 255, 255);
                } else {
                    [$r, $g, $b] = $this->dayColor((string)$day['class']);
                    $pdf->SetFillColor($r, $g, $b);
                }

                $pdf->Rect($cellX, $cellY, $cellWidth, $cellHeight, 'F');

                if ($day !== null) {
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->SetTextColor(67, 67, 67);
                    $pdf->SetXY($cellX, $cellY + 1.4);
                    $pdf->Cell($cellWidth, 3, (string)$day['number'], 0, 0, 'C');
                }
            }
        }

        $pdf->Rect($x, $y, $width, $height, 'D');
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private function dayColor(string $class): array
    {
        return match ($class) {
            'calendar-day--festiu' => [244, 204, 204],
            'calendar-day--obert' => [252, 229, 205],
            'calendar-day--closed' => [244, 244, 246],
            default => [255, 255, 255],
        };
    }

    private function pdfText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $converted = iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);

        return $converted === false ? $text : $converted;
    }

    /**
     * @return array<string, mixed>
     */
    private function calendarData(): array
    {
        $today = FrozenDate::today();
        $yearsTable = $this->fetchTable('Years');
        $year = $yearsTable->find()
            ->where([
                'datainici <=' => $today,
                'datafi >=' => $today,
            ])
            ->order(['datainici' => 'DESC'])
            ->first();

        if (!$year) {
            $year = $yearsTable->find()
                ->order(['datafi' => 'DESC'])
                ->first();
        }

        if (!$year) {
            throw new NotFoundException(__('No academic year found.'));
        }

        $datainici = FrozenDate::parse($year->datainici);
        $datafi = FrozenDate::parse($year->datafi);

        $openStart = FrozenDate::create($datainici->year, 9, 1);
        $openEnd = FrozenDate::create($datafi->year, 7, 15);

        $festiusTable = $this->fetchTable('Festius');
        $festius = $festiusTable->find()
            ->select(['data'])
            ->where([
                'data >=' => $openStart,
                'data <=' => $openEnd,
            ])
            ->enableHydration(false)
            ->all();

        $festiuDates = [];
        foreach ($festius as $festiu) {
            $festiuDates[(string)FrozenDate::parse($festiu['data'])->format('Y-m-d')] = true;
        }

        $months = $this->buildMonths($openStart, $openEnd, $datainici, $datafi, $festiuDates);
        $courseLabel = sprintf('CURS %d-%02d', $datainici->year, $datafi->year % 100);

        return compact('months', 'courseLabel', 'datainici', 'datafi');
    }

    /**
     * @param array<string, bool> $festiuDates
     * @return array<int, array<string, mixed>>
     */
    private function buildMonths(
        FrozenDate $openStart,
        FrozenDate $openEnd,
        FrozenDate $datainici,
        FrozenDate $datafi,
        array $festiuDates
    ): array {
        $monthNames = [
            1 => 'GENER',
            2 => 'FEBRER',
            3 => 'MARÇ',
            4 => 'ABRIL',
            5 => 'MAIG',
            6 => 'JUNY',
            7 => 'JULIOL',
            8 => 'AGOST',
            9 => 'SETEMBRE',
            10 => 'OCTUBRE',
            11 => 'NOVEMBRE',
            12 => 'DESEMBRE',
        ];

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
                $week[$i] = $this->dayCell($date, $datainici, $datafi, $openStart, $openEnd, $festiuDates);
                $day++;
            }

            $weeks[] = $week;

            while ($day <= $daysInMonth) {
                $week = array_fill(0, 7, null);
                for ($i = 0; $i < 7 && $day <= $daysInMonth; $i++) {
                    $date = $cursor->setDate($cursor->year, $cursor->month, $day);
                    $week[$i] = $this->dayCell($date, $datainici, $datafi, $openStart, $openEnd, $festiuDates);
                    $day++;
                }
                $weeks[] = $week;
            }

            // 🔹 Forcem sempre 6 setmanes perquè la vora inferior
            //     sigui la de l’última fila encara que sigui buida
            while (count($weeks) < 6) {
                $weeks[] = array_fill(0, 7, null);
            }

            $months[] = [
                'label' => $monthNames[(int)$cursor->format('n')],
                'weeks' => $weeks,
            ];


            $cursor = $cursor->addMonths(1);
        }

        return $months;
    }

    /**
     * @param array<string, bool> $festiuDates
     * @return array{number:int, class:string}
     */
    private function dayCell(
        FrozenDate $date,
        FrozenDate $datainici,
        FrozenDate $datafi,
        FrozenDate $openStart,
        FrozenDate $openEnd,
        array $festiuDates
    ): array {
        $dateKey = $date->format('Y-m-d');
        $isWeekend = (int)$date->format('N') >= 6;
        $class = 'calendar-day--closed';

        if ($isWeekend || isset($festiuDates[$dateKey])) {
            $class = 'calendar-day--festiu';
        } elseif ($date >= $datainici && $date <= $datafi) {
            $class = 'calendar-day--lectiu';
        } elseif ($date >= $openStart && $date <= $openEnd) {
            $class = 'calendar-day--obert';
        }

        return [
            'number' => (int)$date->format('j'),
            'class' => $class,
        ];
    }
}
