<?php
define('LAYOUT', 'json');

if (!isset($_GET['parent'])) {
    error_response('Parent not specified');
}

if (!preg_match('/^([a-z]+):([a-z]+)=([0-9][0-9]*)$/', $_GET['parent'])) {
    error_response('Invalid parent specification');
}

$linetype = Linetype::load(LINETYPE_NAME);

$lines = [
    (object) [
        'id' => LINE_ID,
        'parent' => $_GET['parent'],
    ]
];

$result = $linetype->unlink($lines);

return [
    'data' => $result,
];