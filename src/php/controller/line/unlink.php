<?php
define('LAYOUT', 'json');

$linetype = Linetype::load(LINETYPE_NAME);

@list($line) = $linetype->find_lines($_SESSION['AUTH'], [(object)['field' => 'id', 'cmp' => '=', 'value' => LINE_ID]]);

if (!$line) {
    return [
        'data' => (object) [],
    ];
}

return [
    'data' => $linetype->unlink($line, PARNT),
];
