<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenDate;

class CalendarController extends AppController
{
    public function index(): void
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

        $this->set(compact('months', 'courseLabel', 'datainici', 'datafi'));
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
