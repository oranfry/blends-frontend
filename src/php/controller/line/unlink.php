<?php
define('LAYOUT', 'json');

if (!isset($_GET['parentid']) || !isset($_GET['parenttype'])) {
    error_response('Parent not specified');
}

if (!preg_match('/^[0-9]+$/', @$_GET['parentid']) || !preg_match('/^[a-z]+$/', @$_GET['parenttype'])) {
    error_response('Invalid parent specifications');
}

$result = Linetype::unlink(LINETYPE_NAME, LINE_ID, $_GET['parenttype'], $_GET['parentid']);

return [
    'data' => $result,
];