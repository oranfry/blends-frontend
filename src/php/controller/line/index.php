<?php
$linetype = Linetype::info(LINETYPE_NAME);
$linetype_lookup = [LINETYPE_NAME => $linetype];
$tablelink_lookup = [];

$parenttype = null;
$parentlink = null;
$parentid = null;

if (LINE_ID) {
    $line = Linetype::get(LINETYPE_NAME, LINE_ID);

    if (!$line) {
        error_response('No such line', 400);
    }

    $child_sets = [];

    foreach ($linetype->children as $child) {
        $childset = Linetype::childset(LINETYPE_NAME, LINE_ID, $child->label);

        if (!isset($linetype_lookup[$child->linetype])) {
            $linetype_lookup[$child->linetype] = Linetype::info($child->linetype);
        }

        if (!$childset) {
            error_response('No such childset: ' . $child->label, 400);
        }

        $child_sets[$child->label] = $childset;
    }
}

foreach ($linetype->parenttypes as $_parenttype) {
    if (!@$_GET[$_parenttype]) {
        continue;
    }

    $pt = Linetype::load($_parenttype);

    foreach (@$pt->children ?: [] as $c) {
        if ($c->parent_link == @$_GET['parentlink']) {
            $parenttype = $pt->name;
            $parentlink = $c->parent_link;
            $parentid = (int) $_GET[$parenttype];
            break 2;
        }
    }
}

foreach (@$linetype->children ?: [] as $child) {
    $tablelink_lookup[$child->parent_link] = Tablelink::info($child->parent_link);
}

$suggested_values = Linetype::suggested(LINETYPE_NAME);

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
