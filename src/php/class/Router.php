<?php
class Router
{
    private static $routes = [
        '/([a-z]+)/blend/([a-z]+)' => ['PACKAGE_NAME', 'BLEND_NAME', 'PAGE' => 'blend/index'],
        '/([a-z]+)/([a-z]+)' => ['PACKAGE_NAME', 'LINETYPE_NAME', 'LINE_ID' => null, 'PAGE' => 'line/index'],
        '/([a-z]+)/([a-z]+)/save' => ['PACKAGE_NAME', 'LINETYPE_NAME', 'LINE_ID' => null, 'PAGE' => 'line/save'],
        '/([a-z]+)/([a-z]+)/([0-9]+)' => ['PACKAGE_NAME', 'LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/index'],
        '/([a-z]+)/([a-z]+)/([0-9]+)/save' => ['PACKAGE_NAME', 'LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/save'],
        '/([a-z]+)/([a-z]+)/([0-9]+)/print' => ['PACKAGE_NAME', 'LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/print'],

        '/api/([a-z]+)/blend/([a-z]+)' => ['PACKAGE_NAME', 'BLEND_NAME', 'PAGE' => 'blend/index', 'JSON' => true],
        '/api/([a-z]+)/blend/([a-z]+)/delete' => ['PACKAGE_NAME', 'BLEND_NAME', 'PAGE' => 'blend/delete'],
        '/api/([a-z]+)/blend/([a-z]+)/update' => ['PACKAGE_NAME', 'BLEND_NAME', 'PAGE' => 'blend/update'],
        '/api/([a-z]+)/blend/([a-z]+)/print' => ['PACKAGE_NAME', 'BLEND_NAME', 'PAGE' => 'blend/print'],
        '/api/([a-z]+)/([a-z]+)/([a-z]+)/add' => ['PACKAGE_NAME', 'BLEND_NAME', 'LINETYPE_NAME', 'PAGE' => 'line/save', 'LINE_ID' => null, 'BULK_ADD' => true],
        '/api/([a-z]+)/([a-z]+)/([0-9]+)/unlink' => ['PACKAGE_NAME', 'LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/unlink'],
        '/api/([a-z]+)/([a-z]+)/([0-9]+)/delete' => ['PACKAGE_NAME', 'LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/delete'],

        '/download/(.*)' => ['FILE', 'PAGE' => 'download'],
    ];

    public static function match($path)
    {
        foreach (static::$routes as $url => $params) {
            if (!preg_match("@^{$url}$@", $path, $groups)) {
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
