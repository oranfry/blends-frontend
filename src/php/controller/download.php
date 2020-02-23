<?php
define('LAYOUT', 'download');

if (preg_match('@/\.\.@', FILE) || preg_match('@^\.\.@', FILE)) {
    error_response('Bad file path');
}

$api = get_api_client();
return $api->file(FILE);
