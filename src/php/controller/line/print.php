<?php
define('LAYOUT', 'json');
$result = Linetype::print(LINETYPE_NAME, LINE_ID);

return [
    'data' => $result,
];
