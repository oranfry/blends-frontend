<?php
namespace blendsfrontend;

class Router extends \Router
{
    protected static $routes = [
        /***************
         *    AUTH     *
         ***************/

        // login
        'GET /' => ['PAGE' => 'login', 'AUTHSCHEME' => 'none'],
        'POST /' => ['PAGE' => 'login', 'AUTHSCHEME' => 'none'],
        'POST /api/auth/login' => ['PAGE' => 'api/login', 'AUTHSCHEME' => 'none', 'LAYOUT' => 'json'],

        // logout
        'POST /(logout)' => ['PAGE', 'AUTHSCHEME' => 'none'],
        'POST /api/auth/logout' => ['PAGE' => 'api/logout', 'AUTHSCHEME' => 'none', 'LAYOUT' => 'json'],

        /***************
         *    LINE     *
         ***************/

        // save
        'POST /([a-z]+)/save' => ['LINETYPE_NAME', 'LINE_ID' => null, 'PAGE' => 'line/save'],
        'POST /api/([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'api/line/save', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'CLI save \S+ \S+ \S+' => ['PAGE', 'USERNAME', 'PASSWORD', 'LINETYPE'],

        // update
        'POST /([a-z]+)/([A-Z0-9]+)/save' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/save'],

        // read
        'GET /([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line'],
        'GET /api/([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/index', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/([a-z]+)/([A-Z0-9]+)/html' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/html', 'AUTHSCHEME' => 'header'],
        'GET /api/([a-z]+)/([A-Z0-9]+)/pdf' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/pdf', 'AUTHSCHEME' => 'header'],

        // delete
        'POST /ajax/([a-z]+)/([A-Z0-9]+)/delete' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/delete'],
        'DELETE /api/([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'api/line/delete', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // unlink
        'POST /ajax/([a-z]+)/([A-Z0-9]+)/unlink/([a-z]+_[a-z]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PARNT', 'PAGE' => 'line/unlink'],
        'POST /api/([a-z]+)/([A-Z0-9]+)/unlink/([a-z]+_[a-z]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PARNT', 'PAGE' => 'api/line/unlink', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // meta
        'GET /api/([a-z]+)/info' => ['LINETYPE_NAME', 'PAGE' => 'api/line/info', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/([a-z]+)/suggested' => ['LINETYPE_NAME', 'PAGE' => 'api/line/suggested', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // print
        'POST /([a-z]+)/([A-Z0-9]+)/print' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/print'],
        'POST /api/([a-z]+)/print' => ['LINETYPE_NAME', 'PAGE' => 'api/line/print', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        /***************
         *    BLEND    *
         ***************/

        // create
        'POST /ajax/([a-z]+)/([a-z]+)/add' => ['BLEND_NAME', 'LINETYPE_NAME', 'PAGE' => 'line/save', 'LINE_ID' => null, 'BULK_ADD' => true],

        // read
        'GET /blend/([a-z]+)' => ['BLEND_NAME', 'PAGE' => 'blend'],
        'GET /api/blend/([a-z]+)/search' => ['BLEND_NAME', 'PAGE' => 'api/blend/index', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/blend/list' => ['PAGE' => 'api/blend/list', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/blend/([a-z]+)/summary' => ['BLEND_NAME', 'PAGE' => 'api/blend/summary', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // update
        'POST /ajax/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'blend/update'],
        'GET /api/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'api/blend/update', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // delete
        'POST /ajax/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'blend/delete'],
        'DELETE /api/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'api/blend/delete', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // meta
        'GET /api/blend/([a-z]+)/info' => ['BLEND_NAME', 'PAGE' => 'api/blend/info', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // print
        'POST /ajax/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'blend/print'],
        'GET /api/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'api/blend/print', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        /***************
         *   FILES     *
         ***************/

        'GET /api/download/(.*)' => ['FILE', 'PAGE' => 'api/download'],
        'GET /api/file/(.*)' => ['FILE', 'PAGE' => 'api/file', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /download/(.*)' => ['FILE', 'PAGE' => 'download'],

        /***************
         *  FRONTEND   *
         ***************/

        'GET /([a-z]+)' => ['LINETYPE_NAME', 'LINE_ID' =>  null, 'PAGE' => 'line'],
        'POST /(change-token)' => ['PAGE'],
        'POST /(switch-user)' => ['PAGE'],

        /***************
         *  API ONLY   *
         ***************/

        'GET /api/touch' => ['PAGE' => 'api/touch', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        /***************
         *  CLI ONLY   *
         ***************/

        'CLI collisions \S+ \S+' => ['PAGE', 'MAX', 'TABLE'],
        'CLI collisions \S+' => ['PAGE', 'MAX', 'TABLE' => null],
        'CLI export|import|expunge-tokens|reset-schema \S+ \S+' => ['PAGE', 'USERNAME', 'PASSWORD'],
        'CLI h2n \S+ \S+' => ['PAGE', 'TABLE', 'H'],
        'CLI n2h \S+ \S+' => ['PAGE', 'TABLE', 'N'],
   ];
}
