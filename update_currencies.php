<?php
/*
 * Load the source currencies
 */
$source = require __DIR__ .'/vendor/moneyphp/iso-currencies/resources/current.php';

$currencies = [];
foreach ($source as $code => $currency) {
    $currencies[$code] = [
        'numeric' => (string) $currency['numericCode'],
        'decimals' => $currency['minorUnit'],
    ];
}

$result = var_export($currencies, true);

file_put_contents(__DIR__ .'/resources/currencies.php', '<?php return ' . $result . ';');