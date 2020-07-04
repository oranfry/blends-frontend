<?php
namespace blendsfrontend;

class Router extends \Router
{
    protected static $routes = [
        'GET /' => ['PAGE' => 'login'],
        'GET /([a-z]+)' => ['LINETYPE_NAME', 'LINE_ID' =>  null, 'PAGE' => 'line'],
        'GET /([a-z]+)/([0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line'],
        'GET /blend/([a-z]+)' => ['BLEND_NAME', 'PAGE' => 'blend'],
        'GET /download/(.*)' => ['FILE', 'PAGE' => 'download'],
        'POST /' => ['PAGE' => 'login'],
        'POST /([a-z]+)/([0-9]+)/print' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/print'],
        'POST /([a-z]+)/([0-9]+)/save' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/save'],
        'POST /([a-z]+)/save' => ['LINETYPE_NAME', 'LINE_ID' => null, 'PAGE' => 'line/save'],
        'POST /api/([a-z]+)/([0-9]+)/delete' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/delete'],
        'POST /api/([a-z]+)/([0-9]+)/unlink/([a-z]+_[a-z]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PARNT', 'PAGE' => 'line/unlink'],
        'POST /api/([a-z]+)/([a-z]+)/add' => ['BLEND_NAME', 'LINETYPE_NAME', 'PAGE' => 'line/save', 'LINE_ID' => null, 'BULK_ADD' => true],
        'POST /api/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'blend/delete'],
        'POST /api/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'blend/print'],
        'POST /api/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'blend/update'],
    ];
}
