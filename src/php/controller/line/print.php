<?php
define('LAYOUT', 'json');
$api = get_api_client();
$result = $api->print(LINETYPE_NAME, LINE_ID);

return [
    'data' => $result,
];
