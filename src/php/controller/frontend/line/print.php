<?php
define('LAYOUT', 'json');

$linetype = Linetype::load(AUTH_TOKEN, LINETYPE_NAME);

$result = $linetype->print(AUTH_TOKEN, [(object) [
    'field' => 'id',
    'value' => LINE_ID,
]]);

return [
    'data' => $result,
];
