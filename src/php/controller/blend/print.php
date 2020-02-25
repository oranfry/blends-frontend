<?php
define('LAYOUT', 'json');

$blend = Blend::info(BLEND_NAME);

apply_filters();

$filters = get_current_filters($blend->fields);
$result = Blend::print(BLEND_NAME, $filters);

return [
    'data' => $result,
];
