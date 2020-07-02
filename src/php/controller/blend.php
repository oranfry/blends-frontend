<?php
use contextvariableset\Daterange;
use contextvariableset\Value;
use contextvariableset\Filter;
use contextvariableset\Hidden;
use contextvariableset\Showas;

$blends = [];
$blend_lookup = [];

foreach (array_keys(Config::get()->blends) as $name) {
    $_blend = Blend::load($name);
    $blends[] = $_blend;
    $blend_lookup[$name] = $_blend;
}

unset($_blend);

$blend = $blend_lookup[BLEND_NAME];
$all_fields = $blend->fields;

$types = array_values(
    array_filter(
        $blend->linetypes,
        function ($v) use ($blend) {
            return !@$blend->hide_types || !in_array($v, array_keys($blend->hide_types));
        }
    )
);

$linetypes = array_map(function($v){
    return Linetype::load($v);
}, $blend->linetypes);

$classes = filter_objects($all_fields, 'type', 'is', 'class');
$fields = filter_objects(filter_objects($all_fields, 'hide', 'not', true), 'type', 'not', 'class');

$generic = (object) [];
$generic_builder = [];

foreach ($all_fields as $field) {
    $generic_builder[$field->name] = [];
}

if (@$blend->groupby) {
    $groupfield = $blend->groupby;
} else {
    $groupable_fields = filter_objects($fields, 'groupable', 'is', true);
    $groupfield = 'group';

    if (count($groupable_fields)) {
        if (count($groupable_fields) > 1) {
            $groupby = new Value('groupby');
            $groupby->options = map_objects($groupable_fields, 'name');

            ContextVariableSet::put('groupby', $groupby);

            foreach ($groupable_fields as $groupable_field) {
                if ($groupby->value == $groupable_field->name) {
                    $groupfield = $groupable_field->name;
                }
            }
        }
    }
}

foreach ($all_fields as $field) {
    if (!@$field->main) {
        continue;
    }

    if ($field->type == 'date') {
        $daterange = new Daterange('daterange');
        ContextVariableSet::put('daterange', $daterange);
    } else {
        $cvs = new Value(BLEND_NAME . "_{$field->name}");
        $cvs->label = $field->name;

        if (@$field->filteroptions) {
            $cvs->options = method_exists($field, 'filteroptions') ? $field->filteroptions() : $field->filteroptions;
        }

        ContextVariableSet::put($field->name, $cvs);
    }
}

apply_filters();

$filters = array_merge(@$blend->filters ?? [], get_current_filters($all_fields));

if (is_string(@$blend->cum)) {
    $cum = false;

    foreach ($filters as $filter) {
        if ($filter->field == $blend->cum) {
            $cum = true;
        }
    }
} else {
    $cum = @$blend->cum;
}

foreach ($fields as $field) {
    if (@$field->summary_if) {
        $field_summary = $field->summary;
        $field->summary = false;

        foreach ($filters as $filter) {
            if ($filter->field == $field->summary_if) {
                $field->summary = $field_summary;
            }
        }
    }
}

$records = $blend->search($filters);

foreach ($records as $record) {
    foreach ($all_fields as $field) {
        if (!in_array($record->{$field->name}, $generic_builder[$field->name])) {
            $generic_builder[$field->name][] = $record->{$field->name};
        }
    }
}

foreach ($filters as $filter) {
    if (
        @$filter->cmp == 'like'
        &&
        strpos($filter->value, '%') === false
    ) {
        $fields = filter_objects($fields, 'name', 'not', $filter->field);
    }
}

if ($groupfield) {
    $fields = filter_objects($fields, 'name', 'not', $groupfield);
    $groupby_field = @filter_objects($all_fields, 'name', 'is', $groupfield)[0];

    if ($groupby_field) {
        usort($records, function ($a, $b) use ($groupby_field) {
            $fieldname = $groupby_field->name;

            if (in_array($groupby_field->type, ['date', 'text'])) {
                return
                    strcmp($a->{$fieldname}, $b->{$fieldname}) ?:
                    ($a->id - $b->id) ?:
                    0;
            }

            if ($groupby_field->type == 'number') {
                return
                    ($a->{$fieldname} <=> $b->{$fieldname}) ?:
                    ($a->id - $b->id) ?:
                    0;
            }

            error_response("cant sort by {$fieldname}, type {$groupby_field->type}");
        });
    }
}

if ($blend->cum_toggle && !@$cum) {
    activate_contextvariableset('boolean', 'cum_summaries', 'cumulative');
}

if (count(filter_objects($fields, 'summary', 'is', 'sum'))) {
    $balances = [];
    $summaries = [];

    if ($blend->past && @$daterange && $daterange->from) {
        $summary_filters = array_merge(@$blend->filters ?? [], get_past_filters($all_fields));
        $past_summary = $blend->summary($summary_filters);
        $summaries = [
            'initial' => $past_summary,
        ];

        foreach ($all_fields as $field) {
            if (@$field->summary != 'sum') {
                continue;
            }

            $balances[$field->name] = @$summaries['initial']->{$field->name} ?: '0.00';
        }
    }

    foreach ($records as $record) {
        foreach ($fields as $_field) {
            if (!@$_field->summary == 'sum') {
                continue;
            }

            if (!isset($summaries[$record->{$groupfield}])) {
                $summaries[$record->{$groupfield}] = (object) [];
            }

            if (!property_exists($summaries[$record->{$groupfield}], $_field->name)) {
                $summaries[$record->{$groupfield}]->{$_field->name} = (@$cum_summaries_bool || @$cum) ? $balances[$_field->name] : '0.00';
            }

            $new_balance = bcadd($summaries[$record->{$groupfield}]->{$_field->name}, $record->{$_field->name}, 2);

            $summaries[$record->{$groupfield}]->{$_field->name} = $new_balance;
            $balances[$_field->name] = $new_balance;
        }
    }
}

foreach ($generic_builder as $field => $values) {
    if (count($values) == 1) {
        $generic->{$field} = $values[0];
    }
}

if (count($blend->showass) > 1) {
    $showas = new Showas(BLEND_NAME . "_showas");
    $showas->options = $blend->showass;
    ContextVariableSet::put('showas', $showas);
    define('SHOWAS', $showas->value ?: @$blend->showass[0] ?: 'list');
    $showas->value = SHOWAS;
} else {
    define('SHOWAS', @$blend->showass[0] ?: 'list');
}

$graphfield = @$blend->graphfield;
$datefield = @filter_objects($all_fields, 'type', 'is', 'date')[0];
$datefieldwhichisgroupfield = $datefield->name == $groupfield ? $datefield : null;

if ($datefieldwhichisgroupfield) {
    $currentgroup = date('Y-m-d');
    $defaultgroup = (date('Y-m-d') >= $daterange->from && date('Y-m-d') <= $daterange->to) ? date('Y-m-d') : $daterange->from;
}

$prepop = [];

foreach ($filters as $filter) {
    if (property_exists($filter, 'value') && !is_array($filter->value)) {
        if ($filter->cmp == '=') {
            $prepop[$filter->field] = $filter->value;
        } elseif ($filter->cmp == 'like') {
            $prepop[$filter->field] = str_replace('%', '', $filter->value);
        }
    }
}

return [
    'records' => $records,
    'blend_lookup' => $blend_lookup,
    'linetypes' => $linetypes,
    'classes' => $classes,
    'fields' => $fields,
    'all_fields' => $all_fields,
    'types' => $types,
    'generic' => $generic,
    'groupfield' => $groupfield,
    'currentgroup' => @$currentgroup,
    'defaultgroup' => @$defaultgroup,
    'graphfield' => $graphfield,
    'summaries' => @$summaries,
    'prepop' => $prepop,
    'datefield' => $datefield,
];
