<?php
define('LAYOUT', 'json');

$linetype = Linetype::load(@$_SESSION['AUTH'], LINETYPE_NAME);

$result = $linetype->print($_SESSION['AUTH'], [(object) [
    'field' => 'id',
    'value' => LINE_ID,
]]);

return [
    'data' => $result,
];
