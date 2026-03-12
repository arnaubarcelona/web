<?php
declare(strict_types=1);

use Cake\ORM\TableRegistry;

if (!function_exists('paginesGetConfigValue')) {
    function paginesGetConfigValue(string $configName): string
    {
        $configsTable = TableRegistry::getTableLocator()->get('Configs');
        $config = $configsTable->find()
            ->select(['valuetext'])
            ->where(['name' => $configName])
            ->first();

        return trim((string)($config->valuetext ?? ''));
    }
}

if (!function_exists('paginesGetYearMaxDate')) {
    function paginesGetYearMaxDate(string $field): ?\DateTimeInterface
    {
        $yearsTable = TableRegistry::getTableLocator()->get('Years');
        $year = $yearsTable->find()
            ->select([$field])
            ->where(["{$field} IS NOT" => null])
            ->order([$field => 'DESC'])
            ->first();

        $date = $year?->{$field};
        return $date instanceof \DateTimeInterface ? $date : null;
    }
}

if (!function_exists('paginesGetLatestYearFieldValue')) {
    function paginesGetLatestYearFieldValue(string $field): string
    {
        $yearsTable = TableRegistry::getTableLocator()->get('Years');
        $year = $yearsTable->find()
            ->select([$field])
            ->where(["{$field} IS NOT" => null])
            ->order(['id' => 'DESC'])
            ->first();

        return trim((string)($year?->{$field} ?? ''));
    }
}

if (!function_exists('paginesFormatCatalanDate')) {
    function paginesFormatCatalanDate(?\DateTimeInterface $date): string
    {
        if (!$date) {
            return '';
        }

        $weekDays = [
            0 => 'diumenge',
            1 => 'dilluns',
            2 => 'dimarts',
            3 => 'dimecres',
            4 => 'dijous',
            5 => 'divendres',
            6 => 'dissabte',
        ];

        $months = [
            1 => 'gener',
            2 => 'febrer',
            3 => 'març',
            4 => 'abril',
            5 => 'maig',
            6 => 'juny',
            7 => 'juliol',
            8 => 'agost',
            9 => 'setembre',
            10 => 'octubre',
            11 => 'novembre',
            12 => 'desembre',
        ];

        $weekday = $weekDays[(int)$date->format('w')] ?? '';
        $day = (int)$date->format('j');
        $month = $months[(int)$date->format('n')] ?? '';

        return trim(sprintf('%s %d de %s', $weekday, $day, $month));
    }
}

if (!function_exists('paginesGetFestiuDateMap')) {
    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @return array<string, bool>
     */
    function paginesGetFestiuDateMap(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $festiusTable = TableRegistry::getTableLocator()->get('Festius');
        $rows = $festiusTable->find()
            ->select(['data'])
            ->where([
                'data >=' => $start->format('Y-m-d'),
                'data <=' => $end->format('Y-m-d'),
            ])
            ->all();

        $map = [];
        foreach ($rows as $row) {
            if (empty($row->data)) {
                continue;
            }

            $map[$row->data->format('Y-m-d')] = true;
        }

        return $map;
    }
}

if (!function_exists('paginesNoWrapText')) {
    function paginesNoWrapText(string $text): string
    {
        return str_replace(' ', "\u{00A0}", trim($text));
    }
}
