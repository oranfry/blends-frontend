<?php
define('LAYOUT', 'json');

$api = get_api_client();
$blend = $api->blend(BLEND_NAME);

apply_filters();

$filters = get_current_filters($blend->fields);
$result = $api->bulkprint(BLEND_NAME, $filters);

return [
    'data' => $result,
];
