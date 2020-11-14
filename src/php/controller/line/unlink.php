<?php
define('LAYOUT', 'json');

$linetype = Linetype::load(@$_SESSION['AUTH'], LINETYPE_NAME);

@list($line) = $linetype->find_lines($_SESSION['AUTH'], [(object)['field' => 'id', 'cmp' => '=', 'value' => LINE_ID]]);

if (!$line) {
    return [
        'data' => (object) [],
    ];
}

return [
    'data' => $linetype->unlink($_SESSION['AUTH'], $line, PARNT),
];
