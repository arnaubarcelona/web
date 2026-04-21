<?php
declare(strict_types=1);

require_once __DIR__ . '/_pagines_dynamic_utils.php';

$getFirstConfigValue = static function (array $keys): string {
    foreach ($keys as $key) {
        $value = trim(paginesGetConfigValue((string)$key));
        if ($value !== '') {
            return $value;
        }
    }

    return '';
};

$address = $getFirstConfigValue(['adreca_centre', 'adrecacentre', 'adreca']);
$postalCode = $getFirstConfigValue(['postalcodecentre', 'postalcode_centre', 'postalcode']);
$cityName = $getFirstConfigValue(['citynamecentre', 'cityname_centre', 'cityname', 'poblacio', 'poblaciocentre']);
$email = $getFirstConfigValue(['mailcentre', 'emailcentre', 'email']);
$phone = $getFirstConfigValue(['telefoncentre', 'telefon_centre', 'telefon', 'telcentre']);

$cityLine = trim(trim($postalCode . ' ' . $cityName));
$phoneHref = preg_replace('/\s+/', '', $phone) ?? '';
?>
<div><?= h($address) ?></div>
<div><?= h($cityLine) ?></div>
<div>
    <?php if ($email !== ''): ?>
        <a href="mailto:<?= h($email) ?>"><?= h($email) ?></a>
    <?php endif; ?>
</div>
<div>
    <?php if ($phone !== ''): ?>
        <a href="tel:<?= h($phoneHref) ?>"><?= h($phone) ?></a>
    <?php endif; ?>
</div>
