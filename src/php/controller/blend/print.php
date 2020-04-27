<?php
define('LAYOUT', 'json');

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

$result = $blend->print($filters);

return [
    'data' => $result,
];
