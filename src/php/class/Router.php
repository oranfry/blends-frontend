<?php
class Router
{
    private static $routes = [
        'GET /([a-z]+)' => ['LINETYPE_NAME', 'LINE_ID' =>  null, 'PAGE' => 'line'],
        'GET /([a-z]+)/([0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line'],
        'GET /blend/([a-z]+)' => ['BLEND_NAME', 'PAGE' => 'blend'],
        'GET /download/(.*)' => ['FILE', 'PAGE' => 'download'],
        'POST /([a-z]+)/([0-9]+)/print' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/print'],
        'POST /([a-z]+)/([0-9]+)/save' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/save'],
        'POST /([a-z]+)/save' => ['LINETYPE_NAME', 'LINE_ID' => null, 'PAGE' => 'line/save'],
        'POST /api/([a-z]+)/([0-9]+)/delete' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/delete'],
        'POST /api/([a-z]+)/([0-9]+)/unlink' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/unlink'],
        'POST /api/([a-z]+)/([a-z]+)/add' => ['BLEND_NAME', 'LINETYPE_NAME', 'PAGE' => 'line/save', 'LINE_ID' => null, 'BULK_ADD' => true],
        'POST /api/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'blend/delete'],
        'POST /api/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'blend/print'],
        'POST /api/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'blend/update'],
    ];

    public static function match($path)
    {
        foreach (static::$routes as $route => $params) {
            if (!preg_match('/^(GET|POST|DELETE)\s+(\S+)/', $route, $groups)) {
                error_response("Invalid route: {$route}");
            }

            list(, $method, $pattern) = $groups;

            if ($method != $_SERVER['REQUEST_METHOD']) {
                continue;
            }

            if (!preg_match("@^{$pattern}$@", $path, $groups)) {
                continue;
            }

            array_shift($groups);

            $page_params = [];

            foreach ($groups as $i => $group) {
                if (!isset($params[$i])) {
                    error_response('Routing error', 500);
                }

                $page_params[$params[$i]] = $group;
            }

            foreach ($params as $key => $value) {
                if (!is_int($key)) {
                    $page_params[$key] = $value;
                }
            }

            define('PAGE_PARAMS', $page_params);

            foreach ($page_params as $key => $value) {
                define($key, $value);
            }

            return true;
        }

        return false;
    }
}
