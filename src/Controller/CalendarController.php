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
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $margin = 10.0;
        $contentWidth = 190.0;
        $contentHeight = 277.0;
        $gap = 4.0;
        $cols = 4;
        $rows = 3;
        $cellWidth = ($contentWidth - (($cols - 1) * $gap)) / $cols;
        $cellHeight = ($contentHeight - (($rows - 1) * $gap)) / $rows;

        $this->renderAnnualInfoPanel($pdf, $calendar['courseLabel'], $margin, $margin, $cellWidth, $cellHeight);

        foreach ($calendar['months'] as $idx => $month) {
            $slot = $idx + 1; // primera casella reservada pel panell d'informació
            $col = $slot % $cols;
            $row = intdiv($slot, $cols);

            $x = $margin + ($col * ($cellWidth + $gap));
            $y = $margin + ($row * ($cellHeight + $gap));

            $this->renderMonthGrid($pdf, $month, $x, $y, $cellWidth, $cellHeight, [
                'dayAlign' => 'center',
                'dayFontSize' => 7.2,
                'dayTopPadding' => 2.6,
                'dayLineWidth' => 0.2,
                'gridLineWidth' => 0.2,
                'gridLineColor' => [178, 171, 191],
                'lectiuColor' => [255, 255, 255],
            ]);
        }

        $this->respondPdfDownload($pdf, sprintf('calendari-anual-%s.pdf', strtolower(str_replace(' ', '-', $calendar['courseLabel']))));
    }

    public function pdfMonthly(): void
    {
        $calendar = $this->calendarData();
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);

        foreach ($calendar['months'] as $month) {
            $pdf->AddPage();

            $this->renderMonthlyHeader($pdf, $calendar['courseLabel']);
            $this->renderMonthGrid($pdf, $month, 10, 36, 190, 251, [
                'dayAlign' => 'right',
                'dayFontSize' => 11,
                'dayTopPadding' => 2.2,
                'dayRightPadding' => 1.8,
                'dayLineWidth' => 0.8,
                'gridLineWidth' => 0.8,
                'gridLineColor' => [255, 255, 255],
                'lectiuColor' => [239, 239, 239],
            ]);
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

    private function renderAnnualInfoPanel(Fpdi $pdf, string $courseLabel, float $x, float $y, float $width, float $height): void
    {
        $this->drawLogo($pdf, $x, $y, $width, 28);

        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(68, 68, 68);
        $pdf->SetXY($x, $y + 31);
        $pdf->Cell($width, 9, $this->pdfText($courseLabel), 0, 1, 'L');

        $this->drawLegend($pdf, $x, $y + 44, [
            ['label' => 'Obert (lectiu)', 'color' => [255, 255, 255]],
            ['label' => 'Obert (no lectiu)', 'color' => [252, 229, 205]],
            ['label' => 'Festiu', 'color' => [244, 204, 204]],
            ['label' => 'Tancat', 'color' => [244, 244, 246]],
        ], 6.4);
    }

    private function renderMonthlyHeader(Fpdi $pdf, string $courseLabel): void
    {
        $this->drawLogo($pdf, 10, 10, 42, 20);

        $pdf->SetTextColor(68, 68, 68);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetXY(56, 12);
        $pdf->Cell(144, 10, $this->pdfText($courseLabel), 0, 1, 'L');
    }

    private function drawLogo(Fpdi $pdf, float $x, float $y, float $maxWidth, float $maxHeight): void
    {
        $logoPath = WWW_ROOT . 'img' . DS . 'logoGran.png';
        if (!is_file($logoPath)) {
            return;
        }

        [$imgWidth, $imgHeight] = getimagesize($logoPath) ?: [0, 0];
        if ($imgWidth <= 0 || $imgHeight <= 0) {
            return;
        }

        $scale = min($maxWidth / $imgWidth, $maxHeight / $imgHeight);
        $drawWidth = $imgWidth * $scale;
        $drawHeight = $imgHeight * $scale;

        $pdf->Image($logoPath, $x, $y, $drawWidth, $drawHeight);
    }

    /**
     * @param array<int, array{label:string,color:array<int,int>}> $legend
     */
    private function drawLegend(Fpdi $pdf, float $x, float $y, array $legend, float $fontSize): void
    {
        $pdf->SetFont('Arial', '', $fontSize);
        $pdf->SetTextColor(67, 67, 67);

        $lineY = $y;
        foreach ($legend as $item) {
            $pdf->SetDrawColor(178, 171, 191);
            $pdf->SetFillColor($item['color'][0], $item['color'][1], $item['color'][2]);
            $pdf->Rect($x, $lineY + 0.6, 4, 4, 'DF');
            $pdf->SetXY($x + 6, $lineY);
            $pdf->Cell(36, 5.2, $this->pdfText($item['label']), 0, 0, 'L');
            $lineY += 7.2;
        }
    }

    /**
     * @param array<string, mixed> $month
     * @param array<string, float|array<int,int>|string> $options
     */
    private function renderMonthGrid(Fpdi $pdf, array $month, float $x, float $y, float $width, float $height, array $options): void
    {
        $headerHeight = 7;
        $weekHeaderHeight = 6;
        $days = ['dl', 'dt', 'dc', 'dj', 'dv', 'ds', 'dg'];

        $dayAlign = (string)($options['dayAlign'] ?? 'center');
        $dayFontSize = (float)($options['dayFontSize'] ?? 7.2);
        $dayTopPadding = (float)($options['dayTopPadding'] ?? 2.6);
        $dayRightPadding = (float)($options['dayRightPadding'] ?? 0);
        $gridLineWidth = (float)($options['gridLineWidth'] ?? 0.2);
        $dayLineWidth = (float)($options['dayLineWidth'] ?? 0.2);
        $gridLineColor = $options['gridLineColor'] ?? [178, 171, 191];
        $lectiuColor = $options['lectiuColor'] ?? [255, 255, 255];

        $pdf->SetDrawColor(178, 171, 191);
        $pdf->SetLineWidth(0.25);

        $pdf->SetFillColor(178, 171, 191);
        $pdf->Rect($x, $y, $width, $headerHeight, 'DF');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY($x, $y + 1.5);
        $pdf->Cell($width, 4, $this->pdfText((string)$month['label']), 0, 0, 'C');

        $tableY = $y + $headerHeight;
        $cellWidth = $width / 7;
        $dayRowsHeight = $height - $headerHeight - $weekHeaderHeight;
        $cellHeight = $dayRowsHeight / 6;

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetFillColor(228, 223, 238);
        $pdf->SetTextColor(67, 67, 67);
        foreach ($days as $i => $day) {
            $cellX = $x + ($i * $cellWidth);
            $pdf->Rect($cellX, $tableY, $cellWidth, $weekHeaderHeight, 'F');
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
                    [$r, $g, $b] = $this->dayColor((string)$day['class'], $lectiuColor);
                    $pdf->SetFillColor($r, $g, $b);
                }

                $pdf->Rect($cellX, $cellY, $cellWidth, $cellHeight, 'F');

                if ($day !== null) {
                    $pdf->SetFont('Arial', '', $dayFontSize);
                    $pdf->SetTextColor(67, 67, 67);
                    $textX = $cellX;
                    $textY = $cellY + $dayTopPadding;

                    if ($dayAlign === 'right') {
                        $textX += $dayRightPadding;
                    }

                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($cellWidth - ($dayAlign === 'right' ? ($dayRightPadding * 2) : 0), 4, (string)$day['number'], 0, 0, $dayAlign === 'right' ? 'R' : 'C');
                }
            }
        }

        $pdf->SetDrawColor($gridLineColor[0], $gridLineColor[1], $gridLineColor[2]);
        $pdf->SetLineWidth($gridLineWidth);

        for ($i = 0; $i <= 7; $i++) {
            $lineX = $x + ($i * $cellWidth);
            $pdf->Line($lineX, $tableY, $lineX, $y + $height);
        }

        $pdf->Line($x, $tableY, $x + $width, $tableY);
        $pdf->Line($x, $tableY + $weekHeaderHeight, $x + $width, $tableY + $weekHeaderHeight);

        for ($i = 1; $i <= 6; $i++) {
            $lineY = $tableY + $weekHeaderHeight + ($i * $cellHeight);
            $pdf->SetLineWidth($dayLineWidth);
            $pdf->Line($x, $lineY, $x + $width, $lineY);
        }

        $pdf->SetLineWidth(0.25);
        $pdf->SetDrawColor(178, 171, 191);
        $pdf->Rect($x, $y, $width, $height, 'D');
    }

    /**
     * @param array<int,int> $lectiuColor
     * @return array{0:int,1:int,2:int}
     */
    private function dayColor(string $class, array $lectiuColor = [255, 255, 255]): array
    {
        return match ($class) {
            'calendar-day--festiu' => [244, 204, 204],
            'calendar-day--obert' => [252, 229, 205],
            'calendar-day--closed' => [244, 244, 246],
            default => [$lectiuColor[0], $lectiuColor[1], $lectiuColor[2]],
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
