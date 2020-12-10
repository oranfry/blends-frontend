<?php
use contextvariableset\Repeater;
use contextvariableset\Daterange;

define('LAYOUT', 'json');

$linetype = Linetype::load(AUTH_TOKEN, LINETYPE_NAME);
$line_template =  json_decode(file_get_contents('php://input'));
$datefield = null;

if (!defined('BULK_ADD') && LINE_ID) {
    $line_template->id = LINE_ID;
}

foreach ($linetype->fields as $field) {
    if ($field->type == 'date') {
        $datefield = $field;
        break; //use the first
    }
}

if ($datefield && defined('BULK_ADD')) {
    $daterange = new Daterange('daterange');

    if (!$daterange->from || !$daterange->to) {
        error_response('Bulk add requires fixed date range');
    }

    $repeater = new Repeater(BLEND_NAME . "_repeater");
    $dates = get_repeater_dates($repeater, $daterange->from, $daterange->to);
} else {
    $dates = [null];
}

$lines = [];

foreach ($dates as $date) {
    $line = clone $line_template;

    if ($datefield && defined('BULK_ADD')) {
        $line->{$datefield->name} = $date;
    }

    $lines[] = $line;
}

return [
    'data' => $linetype->save(AUTH_TOKEN, $lines),
];
