<?php
$api = get_api_client();
$linetype = $api->linetype(LINETYPE_NAME);
$linetype_lookup = [LINETYPE_NAME => $linetype];
$tablelink_lookup = [];

$parenttype = null;
$parentlink = null;
$parentid = null;

if (LINE_ID) {
    $line = $api->get(LINETYPE_NAME, LINE_ID);

    if (!$line) {
        error_response('No such line', 400);
    }

    $child_sets = [];

    foreach ($linetype->children as $child) {
        $childset = $api->children(LINETYPE_NAME, $child->label, LINE_ID);

        if (!isset($linetype_lookup[$child->linetype])) {
            $linetype_lookup[$child->linetype] = $api->linetype($child->linetype);
        }

        if (!$childset) {
            error_response('No such childset: ' . $child->label, 400);
        }

        $child_sets[$child->label] = $childset;
    }
}

foreach ($linetype->parenttypes as $parenttype) {
    if (@$_GET[$parenttype]) {
        $parenttype = $parenttype;
        $parentid = $_GET[$parenttype];
        break;
    }
}

foreach (@$linetype->children ?: [] as $child) {
    $tablelink_lookup[$child->parent_link] = $api->tablelink($child->parent_link);
}

$suggested_values = $api->suggested(LINETYPE_NAME);

$hasFileFields = in_array('file', array_map(function ($f) {
    return $f->type;
}, $linetype->fields));

return [
    'linetype' => $linetype,
    'line' => @$line,
    'linetype_lookup' => $linetype_lookup,
    'tablelink_lookup' => $tablelink_lookup,
    'hasFileFields' => $hasFileFields,
    'suggested_values' => $suggested_values,
    'parentlink' => $parentlink,
    'parenttype' => $parenttype,
    'parentid' => $parentid,
    'child_sets' => @$child_sets,
];
