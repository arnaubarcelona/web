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
        $pdf->AddFont('BebasNeue', '', 'BebasNeue.php');
        $pdf->AddFont('RobotoCondensed', '', 'RobotoCondensed-VariableFont_wght.php');
        $pdf->AddFont('RobotoCondensed', 'B', 'RobotoCondensed-Bold.php');

        $pdf->AddPage();

        $margin = 10.0;
        $contentWidth = 190.0;
        $contentHeight = 277.0;
        $gap = 4.0;
        $cols = 3;
        $rows = 4;
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
                'headerHeight' => 8,
                'weekHeaderHeight' => 8,
                'drawOuterBorder' => true,
                'outerBorderWidth' => 0.8,     // “vora a 8” → gruix 0.8mm (ajusta si vols)
                'dayAlign' => 'center',
                'dayFontSize' => 8.6,
                'dayTopPadding' => 3.1,
                'monthHeaderFontSize' => 13,
                'weekHeaderFontSize' => 8.2,
                'dayLineWidth' => 0.0,
                'gridLineWidth' => 0.0,
                'gridLineColor' => [255, 255, 255],
                'lectiuColor' => [239, 239, 239],
                'drawInnerGrid' => false,
                'dayBold' => false,
            ]);

        }

        $this->respondPdfDownload($pdf, sprintf('calendari-anual-%s.pdf', strtolower(str_replace(' ', '-', $calendar['courseLabel']))));
    }

public function pdfMonthly(): void
{
    $calendar = $this->calendarData();

    $pdf = new Fpdi('P', 'mm', 'A4');
    $pdf->SetAutoPageBreak(false);

    // Fonts (capçaleres = Bebas, cos = Roboto Condensed)
    $pdf->AddFont('BebasNeue', '', 'BebasNeue.php');
    $pdf->AddFont('RobotoCondensed', '', 'RobotoCondensed-VariableFont_wght.php');
    $pdf->AddFont('RobotoCondensed', 'B', 'RobotoCondensed-Bold.php');

    foreach ($calendar['months'] as $month) {
        $pdf->AddPage();

        // Títol del curs
        $this->renderMonthlyHeader($pdf, $calendar['courseLabel'], [
            'courseTitleFontSize' => 30,
        ]);

        // Calendari del mes
        $this->renderMonthGrid($pdf, $month, 10, 38, 190, 249, [
            // Dies
            'dayAlign'        => 'right',
            'dayFontSize'     => 14,
            'dayTopPadding'   => 3.0,
            'dayRightPadding' => 2.2,
            'dayBold'         => true,

            // Capçaleres
            'headerHeight'        => 12, // header del mes
            'weekHeaderHeight'    => 12, // fila dl/dt/...
            'monthHeaderFontSize' => 18,
            'weekHeaderFontSize'  => 10,

            // Header dels dies: sense vores (ni superior, ni interiors, ni inferior)
            'drawWeekHeaderBorders'       => false,
            'drawWeekHeaderTopBorder'     => false,
            'drawWeekHeaderBottomBorder'  => true,

            // Grid
            'drawInnerGrid' => true,
            'gridLineWidth' => 1.1,
            'dayLineWidth'  => 1.1,
            'gridLineColor' => [255, 255, 255],

            // Colors
            'lectiuColor' => [239, 239, 239],

            // Marc exterior
            'drawOuterBorder'  => true,
            'outerBorderWidth' => 1.5,

            'drawSideDoubleLines' => true,

            // lila gruixuda
            'sideLilacWidth' => 1.5,
            'sideLilacColor' => [178, 171, 191],

            // blanc extra
            'sideWhiteWidth' => 2.5,
            'sideWhiteColor' => [255, 255, 255],
            'sideWhiteOffset' => 0.7,

        ]);
    }

    $filename = sprintf(
        'calendari-mensual-%s.pdf',
        strtolower(str_replace(' ', '-', $calendar['courseLabel']))
    );

    $this->respondPdfDownload($pdf, $filename);
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

        $this->setFontHeading($pdf, 24);
        $pdf->SetTextColor(68, 68, 68);
        $pdf->SetXY($x, $y + 31);
        $pdf->Cell($width, 10, $this->pdfText($courseLabel), 0, 1, 'C');

        $this->drawLegend($pdf, $x, $y + 46, [
            ['label' => 'Obert (lectiu)', 'color' => [239, 239, 239]],
            ['label' => 'Obert (no lectiu)', 'color' => [252, 229, 205]],
            ['label' => 'Tancat', 'color' => [244, 204, 204]],
            ['label' => 'Tancat', 'color' => [244, 244, 246]],
        ], 8.2);
    }

    /**
     * @param array{courseTitleFontSize?:float} $options
     */
    private function renderMonthlyHeader(Fpdi $pdf, string $courseLabel, array $options = []): void
    {
        $logo = $this->drawLogo($pdf, 10, 10, 42, 22);

        $titleX = $logo['x2'] + 4;
        $titleWidth = 200 - $titleX;

        $pdf->SetTextColor(68, 68, 68);

        $size = (float)($options['courseTitleFontSize'] ?? 24);
        $this->setFontHeading($pdf, $size);

        $pdf->SetXY($titleX, 12);
        $pdf->Cell($titleWidth, 12, $this->pdfText($courseLabel), 0, 1, 'C');
    }

    /**
     * @return array{x2:float}
     */
    private function drawLogo(Fpdi $pdf, float $x, float $y, float $maxWidth, float $maxHeight): array
    {
        $logoPath = WWW_ROOT . 'img' . DS . 'logoGran.png';
        if (!is_file($logoPath)) {
            return ['x2' => $x + $maxWidth];
        }

        [$imgWidth, $imgHeight] = getimagesize($logoPath) ?: [0, 0];
        if ($imgWidth <= 0 || $imgHeight <= 0) {
            return ['x2' => $x + $maxWidth];
        }

        $scale = min($maxWidth / $imgWidth, $maxHeight / $imgHeight);
        $drawWidth = $imgWidth * $scale;
        $drawHeight = $imgHeight * $scale;

        $pdf->Image($logoPath, $x, $y, $drawWidth, $drawHeight);

        return ['x2' => $x + $drawWidth];
    }

    /**
     * @param array<int, array{label:string,color:array<int,int>}> $legend
     */
    private function drawLegend(Fpdi $pdf, float $x, float $y, array $legend, float $fontSize): void
    {
        $this->setFontBody($pdf, $fontSize);
        $pdf->SetTextColor(67, 67, 67);

        $lineY = $y;
        foreach ($legend as $item) {
            $pdf->SetDrawColor(178, 171, 191);
            $pdf->SetFillColor($item['color'][0], $item['color'][1], $item['color'][2]);
            $pdf->Rect($x, $lineY + 0.6, 4, 4, 'DF');
            $pdf->SetXY($x + 6, $lineY);
            $pdf->Cell(56, 6, $this->pdfText($item['label']), 0, 0, 'L');
            $lineY += 8.4;
        }
    }

    /**
 * @param array<string, mixed> $month
 * @param array<string, mixed> $options
 */
private function renderMonthGrid(
    Fpdi $pdf,
    array $month,
    float $x,
    float $y,
    float $width,
    float $height,
    array $options
): void {
    // ----- Opcions (defaults) -----
    $headerHeight      = (float)($options['headerHeight'] ?? 8);
    $weekHeaderHeight  = (float)($options['weekHeaderHeight'] ?? 7);

    $dayAlign          = (string)($options['dayAlign'] ?? 'center'); // 'center' | 'right'
    $dayFontSize       = (float)($options['dayFontSize'] ?? 8.6);
    $dayTopPadding     = (float)($options['dayTopPadding'] ?? 3.1);
    $dayRightPadding   = (float)($options['dayRightPadding'] ?? 0);

    $monthHeaderFontSize = (float)($options['monthHeaderFontSize'] ?? 13);
    $weekHeaderFontSize  = (float)($options['weekHeaderFontSize'] ?? 8.2);

    $drawInnerGrid     = (bool)($options['drawInnerGrid'] ?? true);
    $dayBold           = (bool)($options['dayBold'] ?? false);

    $gridLineWidth     = (float)($options['gridLineWidth'] ?? 0.2);
    $dayLineWidth      = (float)($options['dayLineWidth'] ?? 0.2);
    $gridLineColor     = $options['gridLineColor'] ?? [178, 171, 191];

    $lectiuColor       = $options['lectiuColor'] ?? [239, 239, 239];

    // ✅ Nou: control de línies del header dels dies (abreviacions)
    $drawWeekHeaderBorders      = (bool)($options['drawWeekHeaderBorders'] ?? true);
    $drawWeekHeaderTopBorder    = (bool)($options['drawWeekHeaderTopBorder'] ?? true);
    $drawWeekHeaderBottomBorder = (bool)($options['drawWeekHeaderBottomBorder'] ?? true);

    $drawSideDoubleLines = (bool)($options['drawSideDoubleLines'] ?? false);

    // Lila gruixuda (ja la vols)
    $sideLilacColor = $options['sideLilacColor'] ?? [178, 171, 191];
    $sideLilacWidth = (float)($options['sideLilacWidth'] ?? 1.5);

    // Blanc extra (la nova)
    $sideWhiteColor  = $options['sideWhiteColor'] ?? [255, 255, 255];
    $sideWhiteWidth  = (float)($options['sideWhiteWidth'] ?? 1.0);

    // Offset del blanc respecte la lila (en mm)
    // (cap a dins de la taula, perquè no es mengi el marge exterior)
    $sideWhiteOffset = (float)($options['sideWhiteOffset'] ?? 0.7);

    // ✅ Nou: vora exterior configurable
    $drawOuterBorder   = (bool)($options['drawOuterBorder'] ?? true);
    $outerBorderWidth  = (float)($options['outerBorderWidth'] ?? 0.25);

    $days = ['dl', 'dt', 'dc', 'dj', 'dv', 'ds', 'dg'];

    // ----- Colors base (marcs per defecte) -----
    $pdf->SetDrawColor(178, 171, 191);
    $pdf->SetLineWidth(0.25);

    // =========================================================
    // 1) HEADER DEL MES (franja + text centrat verticalment)
    // =========================================================
    $pdf->SetFillColor(178, 171, 191);
    $pdf->Rect($x, $y, $width, $headerHeight, 'F');

    $this->setFontHeading($pdf, $monthHeaderFontSize);
    $pdf->SetTextColor(255, 255, 255);

    // Centrat vertical “real” (ajustable si cal)
    $headerTextH = min(6.0, max(4.0, $headerHeight - 2.0));
    $headerTextY = $y + (($headerHeight - $headerTextH) / 2);

    $pdf->SetXY($x, $headerTextY);
    $pdf->Cell($width, $headerTextH, $this->pdfText((string)($month['label'] ?? '')), 0, 0, 'C');

    // =========================================================
    // 2) GEOMETRIA DE LA TAULA
    // =========================================================
    $tableY       = $y + $headerHeight;
    $cellWidth    = $width / 7;
    $dayRowsH     = $height - $headerHeight - $weekHeaderHeight;
    $cellHeight   = $dayRowsH / 6;

    // =========================================================
    // 3) HEADER DELS DIES (abreviacions) - SENSE vores interiors
    // =========================================================
    $this->setFontBody($pdf, $weekHeaderFontSize, true);
    $pdf->SetFillColor(228, 223, 238);
    $pdf->SetTextColor(67, 67, 67);

    // Franja única
    $pdf->Rect($x, $tableY, $width, $weekHeaderHeight, 'F');

    // Text verticalment centrat
    $weekTextH = 3.2;
    $weekTextY = $tableY + (($weekHeaderHeight - $weekTextH) / 2);

    foreach ($days as $i => $dayLabel) {
        $cellX = $x + ($i * $cellWidth);
        $pdf->SetXY($cellX, $weekTextY);
        $pdf->Cell($cellWidth, $weekTextH, $dayLabel, 0, 0, 'C');
    }

    // =========================================================
    // 4) CEL·LES DELS DIES
    // =========================================================
    $weeks = $month['weeks'] ?? [];
    for ($row = 0; $row < 6; $row++) {
        $week = $weeks[$row] ?? array_fill(0, 7, null);

        for ($col = 0; $col < 7; $col++) {
            $day = $week[$col] ?? null;

            $cellX = $x + ($col * $cellWidth);
            $cellY = $tableY + $weekHeaderHeight + ($row * $cellHeight);

            if ($day === null) {
                $pdf->SetFillColor(255, 255, 255);
            } else {
                [$r, $g, $b] = $this->dayColor((string)($day['class'] ?? ''), $lectiuColor);
                $pdf->SetFillColor($r, $g, $b);
            }

            $pdf->Rect($cellX, $cellY, $cellWidth, $cellHeight, 'F');

            if ($day !== null) {
                $this->setFontBody($pdf, $dayFontSize, $dayBold);
                $pdf->SetTextColor(67, 67, 67);

                $textX = $cellX;
                $textY = $cellY + $dayTopPadding;

                // Ajust a la dreta
                $w = $cellWidth;
                if ($dayAlign === 'right') {
                    $textX += $dayRightPadding;
                    $w = $cellWidth - ($dayRightPadding * 2);
                }

                $pdf->SetXY($textX, $textY);
                $pdf->Cell($w, 4.5, (string)($day['number'] ?? ''), 0, 0, $dayAlign === 'right' ? 'R' : 'C');
            }
        }
    }

    // =========================================================
    // 5) LÍNIES (GRID) - CONTROLANT EL HEADER DELS DIES
    // =========================================================
    if ($drawInnerGrid) {
        $pdf->SetDrawColor($gridLineColor[0], $gridLineColor[1], $gridLineColor[2]);
        $pdf->SetLineWidth($gridLineWidth);

        // Línies verticals:
        // - si NO volem vores al header dels dies, comencen sota el header dels dies
        $vStartY = $drawWeekHeaderBorders ? $tableY : ($tableY + $weekHeaderHeight);

        for ($i = 0; $i <= 7; $i++) {
            $lineX = $x + ($i * $cellWidth);
            $pdf->Line($lineX, $vStartY, $lineX, $y + $height);
        }

        // Línia superior del header dels dies (si la vols)
        if ($drawWeekHeaderTopBorder) {
            $pdf->Line($x, $tableY, $x + $width, $tableY);
        }

        // Línia sota el header dels dies (normalment sí)
        if ($drawWeekHeaderBottomBorder) {
            $pdf->Line($x, $tableY + $weekHeaderHeight, $x + $width, $tableY + $weekHeaderHeight);
        }

        // Línies horitzontals entre setmanes
        for ($i = 1; $i <= 6; $i++) {
            $lineY = $tableY + $weekHeaderHeight + ($i * $cellHeight);
            $pdf->SetLineWidth($dayLineWidth);
            $pdf->Line($x, $lineY, $x + $width, $lineY);
        }
    }

    // =========================================================
    // 5bis) DOBLE LÍNIA (LILA + BLANC) A 1a i última columna
    // =========================================================
    if ($drawSideDoubleLines) {
        // zona del calendari (dies + capçalera de dies), NO inclou el header del mes
        $gridTopY = $tableY + $weekHeaderHeight + 1; // només zona de números
        $gridBottomY = $y + $height; // fins a baix del bloc

        // X de la primera separació (vora esquerra de la taula)
        $xLeft = $x;
        // X de l'última separació (vora dreta de la taula)
        $xRight = $x + $width;

        // 1) LILA gruixuda (sobre el grid)
        $pdf->SetDrawColor($sideLilacColor[0], $sideLilacColor[1], $sideLilacColor[2]);
        $pdf->SetLineWidth($sideLilacWidth);
        $pdf->Line($xLeft,  $gridTopY, $xLeft,  $gridBottomY);
        $pdf->Line($xRight, $gridTopY, $xRight, $gridBottomY);

        // 2) BLANC (filet extra cap a dins)
        $pdf->SetDrawColor($sideWhiteColor[0], $sideWhiteColor[1], $sideWhiteColor[2]);
        $pdf->SetLineWidth($sideWhiteWidth);
        $pdf->Line($xLeft + $sideWhiteOffset,  $gridTopY, $xLeft + $sideWhiteOffset,  $gridBottomY);
        $pdf->Line($xRight - $sideWhiteOffset, $gridTopY, $xRight - $sideWhiteOffset, $gridBottomY);

        // Restaura color/amplada per no afectar el següent dibuix
        $pdf->SetDrawColor($gridLineColor[0], $gridLineColor[1], $gridLineColor[2]);
        $pdf->SetLineWidth($gridLineWidth);
    }


    // =========================================================
    // 6) VORA EXTERIOR
    // =========================================================
    if ($drawOuterBorder) {
        $pdf->SetLineWidth($outerBorderWidth);
        $pdf->SetDrawColor(178, 171, 191);
        $pdf->Rect($x, $y, $width, $height, 'D');
    }

    // Restaura (per si després dibuixes altres coses)
    $pdf->SetLineWidth(0.25);
}


    /**
     * @param array<int,int> $lectiuColor
     * @return array{0:int,1:int,2:int}
     */
    private function dayColor(string $class, array $lectiuColor = [239, 239, 239]): array
    {
        return match ($class) {
            'calendar-day--festiu' => [244, 204, 204],
            'calendar-day--obert' => [252, 229, 205],
            'calendar-day--closed' => [244, 244, 246],
            default => [$lectiuColor[0], $lectiuColor[1], $lectiuColor[2]],
        };
    }

    private function setFontHeading(Fpdi $pdf, float $size): void
    {
        $pdf->SetFont('BebasNeue', '', $size);
    }

    private function setFontBody(Fpdi $pdf, float $size, bool $bold = false): void
    {
        $pdf->SetFont('RobotoCondensed', $bold ? 'B' : '', $size);
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
