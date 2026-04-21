<?php
declare(strict_types=1);
?>

<div class="pagines form content">
    <h3><?= __('Ajuda per a elements dinàmics') ?></h3>
    <p><?= __('Pots fer servir aquests noms entre claus dins del cos de la pàgina:') ?></p>
    <ul>
        <li><code>{telefon}</code>: <?= __('valor de Configs.valuetext on name = telefoncentre') ?></li>
        <li><code>{email}</code>: <?= __('valor de Configs.valuetext on name = mailcentre') ?></li>
        <li><code>{contacte}</code>: <?= __('adreça + codi postal/city + email + telèfon del centre des de Configs') ?></li>
        <li><code>{contactevcf}</code>: <?= __('valor de Configs.valuetext on name = urlcontactevcf') ?></li>
        <li><code>{data_inici_preinscripcio}</code>: <?= __('data més gran del camp Years.datainicipreinscripcio (format català)') ?></li>
        <li><code>{data_fi_preinscripcio}</code>: <?= __('data més gran del camp Years.datafipreinscripcio (format català)') ?></li>
        <li><code>{data_inici_reclamacions}</code>: <?= __('data més gran del camp Years.datainicireclamacions (format català)') ?></li>
        <li><code>{data_fi_reclamacions}</code>: <?= __('data més gran del camp Years.datafireclamacions (format català)') ?></li>
        <li><code>{data_llista_admesos}</code>: <?= __('data més gran del camp Years.datallistaadmesos (format català)') ?></li>
        <li><code>{data_inici_matricula}</code>: <?= __('data més gran del camp Years.datainicimatricula (format català)') ?></li>
        <li><code>{data_fi_matricula}</code>: <?= __('data més gran del camp Years.datafimatricula (format català)') ?></li>
        <li><code>{periode_preinscripcio}</code>: <?= __('text "del ... al ..." amb les dates màximes de datainicipreinscripcio i datafipreinscripcio') ?></li>
        <li><code>{periode_reclamacions}</code>: <?= __('text "del ... al ..." amb les dates màximes de datainicireclamacions i datafireclamacions') ?></li>
        <li><code>{periode_matricula}</code>: <?= __('text "del ... al ..." amb les dates màximes de datainicimatricula i datafimatricula') ?></li>
        <li><code>{dies_caducitat_llista_espera}</code>: <?= __('valor del camp Years.diescaducitatllistaespera del registre més recent') ?></li>
        <li><code>{dies_no_justificades}</code>: <?= __('valor del camp Years.diesnojustificades del registre més recent') ?></li>
        <li><code>{horaripreinscripcio}</code>: <?= __('taula d\'horaris (com {horarisatencio}) pels dies entre la data màxima de datainicipreinscripcio i la data màxima de datafipreinscripcio, ambdós inclosos') ?></li>
        <li><code>{horarireclamacions}</code>: <?= __('taula d\'horaris (com {horarisatencio}) pels dies entre la data màxima de datainicireclamacions i la data màxima de datafireclamacions, ambdós inclosos') ?></li>
        <li><code>{horarimatricula}</code>: <?= __('taula d\'horaris (com {horarisatencio}) pels dies entre la data màxima de datainicimatricula i la data màxima de datafimatricula, ambdós inclosos') ?></li>
        <li><code>{horaris}</code>: <?= __('taula d\'horaris de cursos propis (microgrup=0, propi=1) de l\'any més recent + botó de descàrrega en PDF') ?></li>
    </ul>
</div>
