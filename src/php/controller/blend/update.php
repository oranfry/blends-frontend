<?php
define('LAYOUT', 'json');

$data = json_decode(file_get_contents('php://input'));
$blend = Blend::info(BLEND_NAME);

if (@$_GET['selection']) {
    $filters = [
        (object) [
            'field' => 'deepid',
            'cmp' => '=',
            'value' => explode(',', $_GET['selection']),
        ],
    ];
} else {
    apply_filters();

    $filters = get_current_filters($blend->fields);
}

$result = Blend::update(BLEND_NAME, $filters, $data);

return [
    'data' => $result,
];
