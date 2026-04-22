<?php
/**
 * Element: matriculacio
 *
 * Aquest element calcula les dates de matrícula a partir dels valors màxims
 * dels camps de la taula `years` i redirigeix automàticament a la pàgina
 * (taula `pagines`) indicada per cada finestra temporal.
 */

declare(strict_types=1);

use Cake\ORM\TableRegistry;

$Years = TableRegistry::getTableLocator()->get('Years');
$Pagines = TableRegistry::getTableLocator()->get('Pagines');

$yearRows = $Years->find()
    ->select([
        'datafipreinscripcio',
        'databaremprovisional',
        'datafireclamacions',
        'datallistaadmesos',
        'datafimatricula',
        'datasegonamatricula',
    ])
    ->all();

$maxDates = [
    'datafipreinscripcio' => null,
    'databaremprovisional' => null,
    'datafireclamacions' => null,
    'datallistaadmesos' => null,
    'datafimatricula' => null,
    'datasegonamatricula' => null,
];

foreach ($yearRows as $row) {
    foreach (array_keys($maxDates) as $field) {
        if (!empty($row->{$field}) && ($maxDates[$field] === null || $row->{$field} > $maxDates[$field])) {
            $maxDates[$field] = $row->{$field};
        }
    }
}

$tz = new DateTimeZone('Europe/Madrid'); // CET/CEST del centre
$now = new DateTimeImmutable('now', $tz);

$toDateAtTime = static function ($date, string $time, DateTimeZone $timezone): ?DateTimeImmutable {
    if (empty($date)) {
        return null;
    }

    $dateString = method_exists($date, 'format') ? $date->format('Y-m-d') : (string)$date;
    return new DateTimeImmutable($dateString . ' ' . $time, $timezone);
};

$getPageUrlByTitle = static function (string $title) use ($Pagines) {
    $page = $Pagines->find()
        ->select(['id', 'title', 'link'])
        ->where(['title' => $title])
        ->first();

    if (!$page) {
        return null;
    }

    if (!empty($page->link)) {
        return $page->link;
    }

    return ['_name' => 'pagina:view', 'slug' => $page->slug];
};

$startPreinscripcio40 = $toDateAtTime($maxDates['datafipreinscripcio'], '00:00:00', $tz)?->modify('-40 days');
$endPreinscripcio40 = $toDateAtTime($maxDates['datafipreinscripcio'], '23:59:59', $tz);

$startBaremProvisional = $toDateAtTime($maxDates['databaremprovisional'], '20:00:00', $tz);
$endBaremProvisional = $toDateAtTime($maxDates['datafireclamacions'], '23:59:59', $tz);

$startBaremDefinitiu = $toDateAtTime($maxDates['datafireclamacions'], '00:00:00', $tz)?->modify('+1 day');
$endBaremDefinitiu = $toDateAtTime($maxDates['datallistaadmesos'], '20:00:00', $tz);

$startLlistaAdmesos = $toDateAtTime($maxDates['datallistaadmesos'], '20:00:00', $tz);
$endLlistaAdmesos = $toDateAtTime($maxDates['datafimatricula'], '23:59:59', $tz);

$startMatriculaViva = $toDateAtTime($maxDates['datafimatricula'], '00:00:00', $tz);
$startMatriculaSegon = $toDateAtTime($maxDates['datasegonamatricula'], '00:00:00', $tz)?->modify('-30 days');
$endMatriculaSegon = $toDateAtTime($maxDates['datasegonamatricula'], '23:59:59', $tz)?->modify('+2 days');

$targetTitle = 'Matrícula viva';

/**
 * Condicions de redirecció:
 * 1) Si avui és dins els 40 dies anteriors a `datafipreinscripcio` (inclòs),
 *    redirigim a "Preinscripció de juny".
 * 2) Si avui és entre les 20:00 CET de `databaremprovisional` i el final del
 *    dia de `datafireclamacions` (ambdós inclosos), redirigim a
 *    "Barem provisional".
 * 3) Si avui és entre el dia següent a `datafireclamacions` i les 20:00 CET de
 *    `datallistaadmesos` (inclòs), redirigim a "Barem definitiu".
 * 4) Si avui és entre les 20:00 CET de `datallistaadmesos` i el final de
 *    `datafimatricula` (inclòs), redirigim a "Llista d'admesos i espera".
 * 5) Si avui és entre `datafimatricula` i abans dels 30 dies previs a
 *    `datasegonamatricula`, redirigim a "Matrícula viva".
 * 6) Si avui és dins els 30 dies previs a `datasegonamatricula` i fins 2 dies
 *    després (inclòs), redirigim a "Matrícula del 2n quadrimestre".
 * 7) Qualsevol altre cas: "Matrícula viva".
 */
if ($startPreinscripcio40 && $endPreinscripcio40 && $now >= $startPreinscripcio40 && $now <= $endPreinscripcio40) {
    $targetTitle = 'Preinscripció de juny';
} elseif ($startBaremProvisional && $endBaremProvisional && $now >= $startBaremProvisional && $now <= $endBaremProvisional) {
    $targetTitle = 'Barem provisional';
} elseif ($startBaremDefinitiu && $endBaremDefinitiu && $now >= $startBaremDefinitiu && $now <= $endBaremDefinitiu) {
    $targetTitle = 'Barem definitiu';
} elseif ($startLlistaAdmesos && $endLlistaAdmesos && $now >= $startLlistaAdmesos && $now <= $endLlistaAdmesos) {
    $targetTitle = "Llista d'admesos i espera";
} elseif ($startMatriculaSegon && $endMatriculaSegon && $now >= $startMatriculaSegon && $now <= $endMatriculaSegon) {
    $targetTitle = 'Matrícula del 2n quadrimestre';
} elseif ($startMatriculaViva && $startMatriculaSegon && $now >= $startMatriculaViva && $now < $startMatriculaSegon) {
    $targetTitle = 'Matrícula viva';
}

$targetUrl = $getPageUrlByTitle($targetTitle);

if ($targetUrl !== null) {
    echo $this->Html->scriptBlock(
        'window.location.href = ' . json_encode($this->Url->build($targetUrl)) . ';'
    );
}
