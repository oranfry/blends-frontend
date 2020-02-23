<?php
define('LAYOUT', 'json');

$data = json_decode(file_get_contents('php://input'));
$api = get_api_client();
$blend = $api->blend(BLEND_NAME);

apply_filters();

$filters = get_current_filters($blend->fields);
$result = $api->bulkupdate(BLEND_NAME, $data, $filters);

return [
    'data' => $result,
];
