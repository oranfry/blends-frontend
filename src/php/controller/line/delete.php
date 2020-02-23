<?php
define('LAYOUT', 'json');

$api = get_api_client();
$result = $api->delete(LINETYPE_NAME, LINE_ID);
