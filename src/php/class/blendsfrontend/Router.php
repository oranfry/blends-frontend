<?php
namespace blendsfrontend;

class Router extends \Router
{
    protected static $routes = [
        'DELETE /api/([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'api/line/delete'],
        'DELETE /api/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'api/blend/delete'],
        'GET /api/([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/index'],
        'GET /api/([a-z]+)/([A-Z0-9]+)/html' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/html'],
        'GET /api/([a-z]+)/([A-Z0-9]+)/pdf' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/pdf'],
        'GET /api/([a-z]+)/info' => ['LINETYPE_NAME', 'PAGE' => 'api/line/info'],
        'GET /api/([a-z]+)/suggested' => ['LINETYPE_NAME', 'PAGE' => 'api/line/suggested'],
        'GET /api/blend/([a-z]+)/info' => ['BLEND_NAME', 'PAGE' => 'api/blend/info'],
        'GET /api/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'api/blend/print'],
        'GET /api/blend/([a-z]+)/search' => ['BLEND_NAME', 'PAGE' => 'api/blend/index'],
        'GET /api/blend/([a-z]+)/summary' => ['BLEND_NAME', 'PAGE' => 'api/blend/summary'],
        'GET /api/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'api/blend/update'],
        'GET /api/blend/list' => ['PAGE' => 'api/blend/list'],
        'GET /api/download/(.*)' => ['FILE', 'PAGE' => 'api/download'],
        'GET /api/file/(.*)' => ['FILE', 'PAGE' => 'api/file'],
        'GET /api/tablelink/([a-z]+)/info' => ['TABLELINK_NAME', 'PAGE' => 'api/tablelink/info'],
        'GET /api/touch' => ['PAGE' => 'api/touch'],
        'POST /api/auth/login' => ['PAGE' => 'api/login', 'NOAUTH' => true],
        'POST /api/auth/logout' => ['PAGE' => 'api/logout'],
        'POST /api/([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'api/line/save'],
        'POST /api/([a-z]+)/([A-Z0-9]+)/unlink/([a-z]+_[a-z]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PARNT', 'PAGE' => 'api/line/unlink'],
        'POST /api/([a-z]+)/print' => ['LINETYPE_NAME', 'PAGE' => 'api/line/print'],
 
        'GET /' => ['PAGE' => 'login'],
        'GET /([a-z]+)' => ['LINETYPE_NAME', 'LINE_ID' =>  null, 'PAGE' => 'line'],
        'GET /([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line'],
        'GET /blend/([a-z]+)' => ['BLEND_NAME', 'PAGE' => 'blend'],
        'GET /download/(.*)' => ['FILE', 'PAGE' => 'download'],
        'POST /(logout|change-token|switch-user)' => ['PAGE'],
        'POST /' => ['PAGE' => 'login'],
        'POST /([a-z]+)/([A-Z0-9]+)/print' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/print'],
        'POST /([a-z]+)/([A-Z0-9]+)/save' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/save'],
        'POST /([a-z]+)/save' => ['LINETYPE_NAME', 'LINE_ID' => null, 'PAGE' => 'line/save'],
        'POST /ajax/([a-z]+)/([A-Z0-9]+)/delete' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/delete'],
        'POST /ajax/([a-z]+)/([A-Z0-9]+)/unlink/([a-z]+_[a-z]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PARNT', 'PAGE' => 'line/unlink'],
        'POST /ajax/([a-z]+)/([a-z]+)/add' => ['BLEND_NAME', 'LINETYPE_NAME', 'PAGE' => 'line/save', 'LINE_ID' => null, 'BULK_ADD' => true],
        'POST /ajax/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'blend/delete'],
        'POST /ajax/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'blend/print'],
        'POST /ajax/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'blend/update'],

   ];
}
