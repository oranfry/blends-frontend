<?php
use contextvariableset\Daterange;
use contextvariableset\Hidden;
use contextvariableset\Repeater;
use contextvariableset\Value;
use contextvariableset\Filter;

if (!preg_match('/^[a-z]*$/', @$_GET['context'])) {
    error_response("Invalid context");
}

define('CONTEXT', @$_GET['context'] ?: 'default');
define('REFCOL', 'd8b0b0');
define('MAX_COLUMN_WIDTH', 25);
define('BACK', @$_GET['back'] ? base64_decode($_GET['back']) : null);

session_start();

function postroute_app()
{
    switch (AUTHSCHEME) {
        case 'header':
            define('AUTH_TOKEN', @getallheaders()['X-Auth']);

            break;
        case 'cookie':
            define('AUTH_TOKEN', @$_SESSION['AUTH']);

            break;
        case 'none':
            define('AUTH_TOKEN', null);

            break;
        case 'onetime':
            define('AUTH_TOKEN', Blends::login(USERNAME, PASSWORD, true));

            break;
        case 'deny':
            error_response('Access Denied', 403);

        default:
            error_log('Invalid AUTHSCHEME: ' . AUTHSCHEME);
            error_response('Internal Server Error', 500);
    }

    if (
        in_array(AUTHSCHEME, ['header', 'cookie'])
        &&
        (!AUTH_TOKEN || !Blends::verify_token(AUTH_TOKEN))
    ) {
        doover();
    }

    set_highlight(@BlendsConfig::get(AUTH_TOKEN)->highlight ?: REFCOL);
}

function get_current_filters($fields)
{
    $filters = [];

    $daterange = new Daterange('daterange');
    $repeater = new Repeater(BLEND_NAME . "_repeater");
    $datefield = null;

    foreach ($fields as $field) {
        if (!@$field->main) {
            continue;
        }

        if ($field->type == 'date') {
            $datefield = $field;

            if ($daterange->from) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->from,
                    'cmp' => '>=',
                ];
            }

            if ($daterange->to) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->to,
                    'cmp' => '<=',
                ];
            }
        } else {
            $csv = new Value(BLEND_NAME . "_{$field->name}");
            if ($csv->value) {
                $filters[] = (object) [
                    'field' => $field->name,
                    'value' => $csv->value,
                    'cmp' => '=',
                ];
            }
        }
    }

    if ($datefield && $repeater->period) {
        $filters = array_merge($filters, get_repeater_filters($repeater, $datefield->name));
    }

    $filters = array_merge($filters, get_adhoc_filters());

    return $filters;
}

function get_past_filters($fields)
{
    $filters = [];
    $daterange = new Daterange('daterange');
    $repeater = new Repeater(BLEND_NAME . "_repeater");
    $datefield = null;

    foreach ($fields as $field) {
        if ($field->type == 'date') {
            $datefield = $field;

            if ($daterange->from) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->from,
                    'cmp' => '<',
                ];
            }
        } else {
            $csv = new Value(BLEND_NAME . "_{$field->name}");
            if ($csv->value) {
                $filters[] = (object) [
                    'field' => $field->name,
                    'value' => $csv->value,
                    'cmp' => '=',
                ];
            }
        }
    }

    if ($datefield && $repeater->period) {
        $filters = array_merge($filters, get_repeater_filters($repeater, $datefield->name));
    }

    return array_merge($filters, get_adhoc_filters());
}

function get_adhoc_filters()
{
    $filters = [];
    $byfield = [];
    $adhocfilters = ContextVariableSet::get("adhocfilters");

    if ($adhocfilters->value) {
        foreach (explode(',', $adhocfilters->value) as $filterid) {
            $filter = ContextVariableSet::get($filterid);
            $field = $filter->field;

            if (!@$byfield[$field]) {
                $byfield[$field] = [];
            }

            $byfield[$field][] = $filter;
        }
    }

    foreach ($byfield as $field => $_filters) {
        $bycmp = [];

        foreach ($_filters as $filter) {
            $cmp = $filter->cmp;

            if (!@$bycmp[$cmp]) {
                $bycmp[$cmp] = [];
            }

            $bycmp[$cmp][] = $filter;
        }

        if (count(@$bycmp['='] ?: []) > 1) {
            $filters[] = (object) [
                'field' => $field,
                'value' => array_map(function($e){ return $e->value; }, $bycmp['=']),
                'cmp' => '=',
            ];

            unset($bycmp['=']);
        }

        foreach ($bycmp as $cmp => $_filters) {
            foreach ($_filters as $filter) {
                $filters[] = (object) [
                    'field' => $filter->field,
                    'value' => $filter->value,
                    'cmp' => $filter->cmp,
                ];
            }
        }
    }

    return $filters;
}

function get_repeater_filters($repeater, $datefield_name)
{
     return [(object) [
        'cmp' => '*=',
        'field' => $datefield_name,
        'value' => $repeater->render(),
    ]];
}

function computed_field_value($record, $expression)
{
    extract((array)$record);

    return eval("return {$expression};");
}

function set_highlight($hex)
{
    list($h) = hexToHsl($hex);
    list(, $s, $l) = hexToHsl(REFCOL);

    define('HIGHLIGHT', hslToHex([$h, $s, $l]));
}

function hexToHsl($hex)
{
    $hex = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
    $rgb = array_map(function ($part) {
        return hexdec($part) / 255;
    }, $hex);

    $max = max($rgb);
    $min = min($rgb);

    $l = ($max + $min) / 2;

    if ($max == $min) {
        $h = $s = 0;
    } else {
        $diff = $max - $min;
        $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

        switch ($max) {
            case $rgb[0]:
                $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                break;
            case $rgb[1]:
                $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                break;
            case $rgb[2]:
                $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                break;
        }

        $h /= 6;
    }

    return array($h, $s, $l);
}

function hslToHex($hsl)
{
    list($h, $s, $l) = $hsl;

    if ($s == 0) {
        $r = $g = $b = 1;
    } else {
        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $r = hue2rgb($p, $q, $h + 1/3);
        $g = hue2rgb($p, $q, $h);
        $b = hue2rgb($p, $q, $h - 1/3);
    }

    return rgb2hex($r) . rgb2hex($g) . rgb2hex($b);
}

function hue2rgb($p, $q, $t)
{
    if ($t < 0) {
        $t += 1;
    }
    if ($t > 1) {
        $t -= 1;
    }
    if ($t < 1/6) {
        return $p + ($q - $p) * 6 * $t;
    }
    if ($t < 1/2) {
        return $q;
    }
    if ($t < 2/3) {
        return $p + ($q - $p) * (2/3 - $t) * 6;
    }

    return $p;
}

function rgb2hex($rgb)
{
    return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
}

function adjustBrightness($hex, $steps)
{
    $steps = max(-255, min(255, $steps));
    preg_match('/^(#)/', $hex, $groups);
    $hash = @$groups[1] ?: '';
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2).str_repeat(substr($hex, 1, 1), 2).str_repeat(substr($hex, 2, 1), 2);
    }

    $color_parts = str_split($hex, 2);
    $return = $hash;

    foreach ($color_parts as $color) {
        $color = hexdec($color);
        $color = max(0, min(255, $color + $steps));
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
    }

    return $return;
}

function apply_filters()
{
    $adhocfilters = new Hidden(BLEND_NAME . "_filters");
    ContextVariableSet::put('adhocfilters', $adhocfilters);

    if ($adhocfilters->value) {
        foreach (explode(',', $adhocfilters->value) as $filterid) {
            ContextVariableSet::put($filterid, new Filter($filterid));
        }
    }

    $repeater = new Repeater(BLEND_NAME . "_repeater");
    ContextVariableSet::put('repeater', $repeater);
}

function addlink($type, $group, $groupfield, $defaultgroup, $parent_query, $prepop = [])
{
    $url = "/{$type}";

    $query = $prepop;

    if ($groupfield) {
        $query[$groupfield] = $group ?: @$defaultgroup;
    }

    $query['back'] = base64_encode($_SERVER['REQUEST_URI']);

    $url .= '?' . http_build_query($query) . (@$parent_query ? '&' . $parent_query : '');

    return $url;
}

function editlink($id, $type)
{
    $back = base64_encode($_SERVER['REQUEST_URI']);

    return "/{$type}/{$id}?back={$back}";
}

function get_repeater_dates($repeater, $from, $to)
{
    $period = $repeater->period;

    if ($period == 'day') {
        $n = $repeater->n;
        $pegdate = $repeater->pegdate;
        $fastforward = $repeater->ff;
        $offset = '';
    } elseif ($period == 'month') {
        $day = $repeater->day;
        $round = $repeater->round;
        $fastforward = $repeater->ff;
        $offset = $repeater->offset;
    } elseif ($period == 'year') {
        $month = $repeater->month;
        $day = $repeater->day;
        $round = $repeater->round;
        $fastforward = $repeater->ff;
        $offset = $repeater->offset;
    } else {
        error_response("Invalid period");
    }

    if ($offset) {
        if (!preg_match('/^([+-][1-9][0-9]*) (day|month|year)$/', $offset, $groups)) {
            error_response('Invalid offset');
        }

        $offsetMagnitude = intval(preg_replace('/[+-]/', '', $groups[1]));
        $offsetSign = preg_match('/-/', $groups[1]) ? '-' : '+';
        $offsetSignNegated = $offsetSign == '-' ? '+' : '-';
        $offsetPeriod = $groups[2];
    }

    $start = $from;
    $end = $to;

    if ($offset && $offsetMagnitude) {
        $start = date_shift($start, "{$offsetSignNegated}{$offsetMagnitude} {$offsetPeriod}");
        $end = date_shift($end, "{$offsetSignNegated}{$offsetMagnitude} {$offsetPeriod}");
    }

    if ($fastforward) {
        $start = date_shift($start, "-6 day");
        $end = date_shift($end, "-6 day");
    }

    $dates = [];

    for ($d = $start; $d <= $end; $d = date_shift($d, '+1 day')) {
        if ($period == 'day') {
            $a = strtotime("{$d} 00:00:00 +0000") / 86400;
            $b = strtotime("{$pegdate} 00:00:00 +0000") / 86400;

            if (($a - $b) % $n == 0) {
                $dates[] = $d;
            }
        } elseif (
            preg_replace('/.*-/', '', $d) == ($round ? min($day, date('t', strtotime($d))) : $day) &&
            ($period != 'year' || preg_replace('/.*-(.*)-.*/', '$1', $d) == $month)
        ) {
            $dates[] = $d;
        }
    }

    // fastforward and offset

    for ($i = 0; $i < count($dates); $i++) {
        if ($fastforward) {
            while (date('w', strtotime($dates[$i])) != $fastforward - 1) {
                $dates[$i] = date_shift($dates[$i], "+1 day");
            }
        }

        if ($offset && $offsetMagnitude) {
            $dates[$i] = date_shift($dates[$i], "{$offsetSign}{$offsetMagnitude} {$offsetPeriod}");
        }
    }

    return $dates;
}

function doover()
{
    unset($_SESSION['AUTH']);
    header('Location: /');
    die();
}

function get_query_filters()
{
    $filters = [];

    foreach (explode('&', $_SERVER['QUERY_STRING']) as $v) {
        $r = preg_split('/(\*=|>=|<=|~|=|<|>)/', urldecode($v), -1, PREG_SPLIT_DELIM_CAPTURE);

        if (count($r) == 3) {
            $filters[] = (object) [
                'field' => $r[0],
                'cmp' => $r[1],
                'value' => $r[2],
            ];
        }
    }

    return $filters;
}

function build_table_definitions($token)
{
    $schemata = [];
    $definitions = [
        'record' => [
            'sequence_pointer' => "create table `sequence_pointer` (`table` varchar(255) not null, `pointer` int default '1', primary key (`table`)) engine=innodb default charset=latin1",
            'master_record_lock' => "create table `master_record_lock` (`counter` int default null) engine=innodb default charset=latin1",
        ],
        'tablelink' => [],
    ];

    $defs = [
        'text' => "varchar(255) null",
        'numberdp' => 'decimal (32, 16) null',
        'number' => 'integer null',
        'multiline' => "text",
        'date' => "date",
        'timestamp' => "timestamp",
    ];

    $examples = [
        'text' => "'hello'",
        'number' => '1.23',
        'multiline' => "'multiline\ntext'",
        'date' => "'2020-01-01'",
        'timestamp' => "'2020-01-01 01:02:03'",
    ];

    $tablelinks = [];

    foreach (array_keys(BlendsConfig::get($token)->linetypes) as $linetypeName) {
        $linetype = Linetype::load($token, $linetypeName);
        $table = $linetype->table;
        $db_table = BlendsConfig::get($token)->tables[$table];

        if (!isset($schemata[$db_table])) {
            $schemata[$db_table] = [
                'id' => (object) ['def' => 'char(10) not null', 'primary' => true],
                'user' => (object) ['def' => 'varchar(255) default null'],
                'created' => (object) ['def' => 'timestamp not null default current_timestamp'],
            ];
        }

        foreach ($linetype->unfuse_fields as $field => $details) {
            if (!is_object($details) || !property_exists($details, 'type')) {
                error_response("Unfuse field {$linetype->name}.{$field} not in the right format");
            }

           $field_full = str_replace('{t}.', '', $field);

            if (isset($schemata[$db_table][$field_full]) && $schemata[$db_table][$field_full]->def != $details->type) {
                error_response('Inconsistent types for field ' . $db_table . '.' . $field . ': ' . $schemata[$db_table][$field_full]->def . ' vs.' . $details->type);
            }

            $schemata[$db_table][$field_full] = (object) ['def' => $details->type];
        }

        foreach (@$linetype->children as $child) {
            $tablelinks[$child->parent_link] = true;
        }

        foreach (@$linetype->inlinelinks ?: [] as $child) {
            $tablelinks[$child->tablelink] = true;
        }
    }

    foreach ($schemata as $table => $fields) {
        $definitions['record'][$table] = "create table `{$table}` (" . implode(", ", array_map(function($def, $fieldName){
            return "`{$fieldName}` {$def->def}";
        }, $fields, array_keys($fields))) . ", primary key (`id`)) engine=InnoDB default charset=latin1";
    }

    foreach (array_keys($tablelinks) as $tablelinkname) {
        $tablelink = Tablelink::load($tablelinkname);
        $dbtables = [
            BlendsConfig::get($token)->tables[$tablelink->tables[0]],
            BlendsConfig::get($token)->tables[$tablelink->tables[1]],
        ];

        $unique = implode(", ", array_filter([
            in_array($tablelink->type, ['oneone', 'manyone']) ? "UNIQUE KEY `{$tablelink->ids[0]}_id` (`{$tablelink->ids[0]}_id`)" : '',
            in_array($tablelink->type, ['oneone', 'onemany']) ? "UNIQUE KEY `{$tablelink->ids[1]}_id` (`{$tablelink->ids[1]}_id`)" : '',
        ]));

        $unique .= ($unique ? ", " : '');

        $definitions['tablelink'][$tablelink->middle_table] = preg_replace('/\s+/', ' ', "create table `{$tablelink->middle_table}` (
                `{$tablelink->ids[0]}_id` char(10) not null,
                `{$tablelink->ids[1]}_id` char(10) not null,
                {$unique} key `fk_{$tablelinkname}_1` (`{$tablelink->ids[0]}_id`),
                key `fk_{$tablelinkname}_2` (`{$tablelink->ids[1]}_id`),
                constraint `fk_{$tablelinkname}_1` foreign key (`{$tablelink->ids[0]}_id`) references `{$dbtables[0]}` (`id`) on delete cascade on update restrict,
                constraint `fk_{$tablelinkname}_2` foreign key (`{$tablelink->ids[1]}_id`) references `{$dbtables[1]}` (`id`) on delete cascade on update restrict
            ) engine=innodb default charset=latin1");
    }

    return $definitions;
}
