<?php
define('LAYOUT', 'json');

$data = json_decode(file_get_contents('php://input'));
$blend = Blend::load(BLEND_NAME);

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

$result = $blend->update($filters, $data);

return [
    'data' => $result,
];
