<?php
declare(strict_types=1);

require_once __DIR__ . '/_pagines_dynamic_utils.php';

$dataBaremProvisional = paginesFormatCatalanDate(paginesGetYearMaxDate('databaremprovisional'));
$iniciReclamacions = paginesGetYearMaxDate('datainicireclamacions');
$fiReclamacions = paginesGetYearMaxDate('datafireclamacions');
$periodeReclamacions = ($iniciReclamacions && $fiReclamacions)
    ? sprintf('del %s al %s', paginesFormatCatalanDate($iniciReclamacions), paginesFormatCatalanDate($fiReclamacions))
    : '';

$dataLlistaAdmesos = paginesFormatCatalanDate(paginesGetYearMaxDate('datallistaadmesos')) . ' a les 20:00h';

$iniciMatricula = paginesGetYearMaxDate('datainicimatricula');
$fiMatricula = paginesGetYearMaxDate('datafimatricula');
$periodeMatricula = ($iniciMatricula && $fiMatricula)
    ? sprintf('del %s al %s', paginesFormatCatalanDate($iniciMatricula), paginesFormatCatalanDate($fiMatricula))
    : '';

$mailCentre = trim(paginesGetConfigValue('mailcentre'));
$telefonCentre = trim(paginesGetConfigValue('telefoncentre'));
?>
<table class="calendari-preinscripcio" style="width:90%; margin:0 auto; table-layout:fixed; border-collapse:collapse; border:none;">
    <tbody>
        <tr>
            <td style="width:33%; text-align:left !important; vertical-align:middle; border:none; padding:0.45rem 0;">
                <?= h($dataBaremProvisional) ?>
            </td>
            <td style="width:67%; text-align:left !important; vertical-align:middle; border:none; padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Barem provisional</strong>: Comproveu que consteu preinscrits als grups correctes.
            </td>
        </tr>
        <tr>
            <td style="width:33%; text-align:left !important; vertical-align:middle; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0;">
                <?= h($periodeReclamacions) ?>
            </td>
            <td style="width:67%; text-align:left !important; vertical-align:middle; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Reclamacions</strong>: Per&nbsp;<a href="mailto:<?= h($mailCentre) ?>">correu electrònic</a>&nbsp;o per&nbsp;<a href="tel:<?= h($telefonCentre) ?>">telèfon</a>.
            </td>
        </tr>
        <tr>
            <td style="width:33%; text-align:left !important; vertical-align:middle; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0;">
                <strong><?= h($dataLlistaAdmesos) ?></strong>
            </td>
            <td style="width:67%; text-align:left !important; vertical-align:middle; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Llista d’admesos i llista d’espera</strong>: Si esteu en llista d’espera, heu d’esperar que contactem amb vosaltres a partir de setembre.
            </td>
        </tr>
        <tr>
            <td style="width:33%; text-align:left !important; vertical-align:middle; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0;">
                <?= h($periodeMatricula) ?>
            </td>
            <td style="width:67%; text-align:left !important; vertical-align:middle; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Matrícula</strong>: Si heu estat admesos, heu de venir&nbsp;presencialment&nbsp;al centre a fer el pagament de confirmació de la matrícula. Pot venir qualsevol altra persona a fer el tràmit en representació vostra. Si no veniu ni ens comuniqueu res, perdreu la plaça.
            </td>
        </tr>
    </tbody>
</table>
