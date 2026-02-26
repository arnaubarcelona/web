<?php
/**
 * Element: contactecentre
 *
 * Mostra les línies de contacte guardades a la taula configs.
 * - Email → mailto:
 * - Telèfon → tel:
 */

use Cake\ORM\TableRegistry;

$Configs = TableRegistry::getTableLocator()->get('Configs');

$config = $Configs->find()
    ->select(['name', 'valuetext', 'valuelongtext'])
    ->where([
        'LOWER(name) IN' => ['contacte', 'contacte_centre', 'contactecentre', 'centre_contacte', 'contacte centre'],
    ])
    ->first();

if ($config === null) {
    $config = $Configs->find()
        ->select(['name', 'valuetext', 'valuelongtext'])
        ->where([
            'OR' => [
                'Configs.valuelongtext LIKE' => '%@%',
                'Configs.valuetext LIKE' => '%@%',
            ],
        ])
        ->orderAsc('Configs.id')
        ->first();
}

$contactText = trim((string)($config->valuelongtext ?? $config->valuetext ?? ''));
if ($contactText === '') {
    $contactText = "Av. Mare de Déu de Montserrat, 78\n08024 Barcelona\ninfo@cfaguinardo.cat\n+34 934 50 48 37";
}

$lines = preg_split('/\r\n|\r|\n/', $contactText) ?: [];
$lines = array_values(array_filter(array_map('trim', $lines), static fn(string $line): bool => $line !== ''));

foreach ($lines as $line):

    $isEmail = filter_var($line, FILTER_VALIDATE_EMAIL);

    // Detecta telèfon (accepta +, espais i números)
    $isPhone = preg_match('/^\+?[0-9\s]+$/', $line);

    if ($isEmail):
?>
        <div>
            <a href="mailto:<?= h($line) ?>">
                <?= h($line) ?>
            </a>
        </div>
<?php
    elseif ($isPhone):
        // Neteja espais per l'atribut tel:
        $telLink = preg_replace('/\s+/', '', $line);
?>
        <div>
            <a href="tel:<?= h($telLink) ?>">
                <?= h($line) ?>
            </a>
        </div>
<?php
    else:
?>
        <div><?= h($line) ?></div>
<?php
    endif;

endforeach;
?>