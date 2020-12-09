<?php
define('LAYOUT', 'json');

$blend = Blend::load(AUTH_TOKEN, BLEND_NAME);

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

$result = $blend->delete(AUTH_TOKEN, $filters);

return [
    'data' => $result,
];
