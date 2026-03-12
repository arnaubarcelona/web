<?php
declare(strict_types=1);
?>
<table class="calendari-preinscripcio" style="width:90%; margin:0 auto; table-layout:fixed; border-collapse:collapse; border:none;">
    <tbody>
        <tr>
            <td style="width:33%; vertical-align:top; border:none; padding:0.45rem 0;">
                <?= $this->element('data_barem_provisional') ?>
            </td>
            <td style="width:67%; vertical-align:top; border:none; padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Barem provisional</strong>: Comproveu que consteu preinscrits als grups correctes.
            </td>
        </tr>
        <tr>
            <td style="width:33%; vertical-align:top; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0;">
                <?= $this->element('periode_reclamacions') ?>
            </td>
            <td style="width:67%; vertical-align:top; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Reclamacions</strong>: Per&nbsp;correu electrònic&nbsp;o per&nbsp;telèfon.
            </td>
        </tr>
        <tr>
            <td style="width:33%; vertical-align:top; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0;">
                <strong><?= $this->element('data_llista_admesos') ?></strong>
            </td>
            <td style="width:67%; vertical-align:top; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Llista d’admesos i llista d’espera</strong>: Si esteu en llista d’espera, heu d’esperar que contactem amb vosaltres a partir de setembre.
            </td>
        </tr>
        <tr>
            <td style="width:33%; vertical-align:top; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0;">
                <?= $this->element('periode_matricula') ?>
            </td>
            <td style="width:67%; vertical-align:top; border:none; border-top:1px solid rgba(0,0,0,0.2); padding:0.45rem 0 0.45rem 1.15rem;">
                <strong>Matrícula</strong>: Si heu estat admesos, heu de venir&nbsp;presencialment&nbsp;al centre a fer el pagament de confirmació de la matrícula. Pot venir qualsevol altra persona a fer el tràmit en representació vostra. Si no veniu ni ens comuniqueu res, perdreu la plaça.
            </td>
        </tr>
    </tbody>
</table>
