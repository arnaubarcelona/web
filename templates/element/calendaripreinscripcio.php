<?php
declare(strict_types=1);
?>
<table class="calendari-preinscripcio" style="width:100%; table-layout:fixed; border-collapse:collapse;">
    <tbody>
        <tr>
            <td style="width:33.333%; vertical-align:top;"><?= $this->element('data_barem_provisional') ?></td>
            <td style="width:33.333%; vertical-align:top;"><strong>Barem provisional</strong></td>
            <td style="width:33.333%; vertical-align:top;">Comproveu que consteu preinscrits als grups correctes.</td>
        </tr>
        <tr>
            <td style="width:33.333%; vertical-align:top;"><?= $this->element('periode_reclamacions') ?></td>
            <td style="width:33.333%; vertical-align:top;"><strong>Reclamacions</strong></td>
            <td style="width:33.333%; vertical-align:top;">Per&nbsp;correu electrònic&nbsp;o per&nbsp;telèfon.</td>
        </tr>
        <tr>
            <td style="width:33.333%; vertical-align:top;"><strong><?= $this->element('data_llista_admesos') ?></strong>&nbsp;a les 20:00</td>
            <td style="width:33.333%; vertical-align:top;"><strong>Llista d’admesos i llista d’espera</strong></td>
            <td style="width:33.333%; vertical-align:top;">Si esteu en llista d’espera, heu d’esperar que contactem amb vosaltres a partir de setembre.</td>
        </tr>
        <tr>
            <td style="width:33.333%; vertical-align:top;"><?= $this->element('periode_matricula') ?></td>
            <td style="width:33.333%; vertical-align:top;"><strong>Matrícula</strong></td>
            <td style="width:33.333%; vertical-align:top;">Si heu estat admesos, heu de venir&nbsp;presencialment&nbsp;al centre a fer el pagament de confirmació de la matrícula. Pot venir qualsevol altra persona a fer el tràmit en representació vostra. Si no veniu ni ens comuniqueu res, perdreu la plaça.</td>
        </tr>
    </tbody>
</table>
