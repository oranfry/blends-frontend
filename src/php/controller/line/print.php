<?php
define('LAYOUT', 'json');

$linetype = Linetype::load(LINETYPE_NAME);

$result = $linetype->print([
    (object) [
        'field' => 'id',
        'value' => LINE_ID,
    ],
]);

return [
    'data' => $result,
];
