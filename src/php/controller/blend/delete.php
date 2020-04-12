<?php
define('LAYOUT', 'json');

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

$result = Blend::delete(BLEND_NAME, $filters);

return [
    'data' => $result,
];
