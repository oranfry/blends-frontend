<?php
$linetype = Linetype::load(LINETYPE_NAME);
$parenttypes = Linetype::find_parent_linetypes(LINETYPE_NAME);
$linetype_lookup = [LINETYPE_NAME => $linetype];
$tablelink_lookup = [];

$parenttype = null;
$parentlink = null;
$parentid = null;

if (LINE_ID) {
    $line = @$linetype->find_lines([(object)['field' => 'id', 'value' => LINE_ID]])[0];

    if (!$line) {
        error_response('No such line', 400);
    }

    $linetype->load_children($line);
}

foreach ($parenttypes as $_parenttype) {
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

$suggested_values = $linetype->get_suggested_values();

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
];
