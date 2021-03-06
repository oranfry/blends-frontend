<?php
define('LAYOUT', 'json');

$data = json_decode(file_get_contents('php://input'));
$blend = Blend::load(@$_SESSION['AUTH'], BLEND_NAME);

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

$result = $blend->update($_SESSION['AUTH'], $filters, $data);

return [
    'data' => $result,
];
