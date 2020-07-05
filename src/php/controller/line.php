<?php
$linetype = Linetype::load(LINETYPE_NAME);

$parenttype = null;
$parentlink = null;
$parentid = null;

if (LINE_ID) {
    $line = @$linetype->find_lines($_SESSION['AUTH'], [(object)['field' => 'id', 'value' => LINE_ID]])[0];

    if (!$line) {
        error_response('No such line', 400);
    }

    $linetype->load_children($_SESSION['AUTH'], $line);
}

$suggested_values = $linetype->get_suggested_values();

$hasFileFields = in_array('file', array_map(function ($f) {
    return $f->type;
}, $linetype->fields));

return [
    'linetype' => $linetype,
    'line' => @$line,
    'hasFileFields' => $hasFileFields,
    'suggested_values' => $suggested_values,
    'parentlink' => $parentlink,
    'parenttype' => $parenttype,
    'parentid' => $parentid,
];
