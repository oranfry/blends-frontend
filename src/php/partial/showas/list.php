<?php
$lastgroup = 'initial';
$daterange = ContextVariableSet::get('daterange');

if ($daterange) {
    $from = $daterange->from;
    $to = $daterange->to;
}

$num_cols = count($fields);
$num_sacrifice_cols = count(filter_objects($fields, 'sacrifice', 'is', true));
$num_nonsacrifice_cols = $num_cols - $num_sacrifice_cols;
$seen_today = !@$currentgroup || !$daterange || strcmp($currentgroup, $from) < 0 || strcmp($currentgroup, $to) > 0;
?>

<table class="easy-table">
    <thead>
        <tr>
            <th class="when-selecting printhide"><i class="icon icon--smallsquare-o selectall"></i></td></th>
            <?php foreach ($fields as $field): ?>
                <th class="<?= $field->type == 'number' ? 'right' : '' ?> <?= @$field->sacrifice ? 'sacrifice' : '' ?>"><?= !@$field->supress_header && @$field->type != 'icon' ? $field->name : '' ?></th>
            <?php endforeach ?>
            <?php if (!@$hideadd): ?>
                <th><i class="icon icon--smalldot toggle-selecting" style="float: right"></i></th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 0; $i <= count($records); $i++): ?>
            <?php
                $skip = false;
                unset($record);

                if ($i == count($records)) {
                    if (!$seen_today) {
                        $record = (object) [$groupfield => $currentgroup];
                    } else {
                        $record = (object) [@$groupfield => null];
                    }

                    $skip = true;
                } else {
                    $record =& $records[$i];
                }
            ?>

            <?php if (@$summaries[@$lastgroup] && ($i == count($records) || @$groupfield && $record->{$groupfield} != $lastgroup)): ?>
                <?php $summary = $summaries[$lastgroup]; ?>
                <tr>
                    <td class="when-selecting printhide"></td>
                    <?php foreach ($fields as $field): ?>
                        <td class="<?= @$field->sacrifice ? 'sacrifice' : '' ?> <?= $field->type == 'number' ? 'right' : '' ?>">
                            <?php if (@$summary->{$field->name}): ?>
                                <strong><?= @$field->prefix . $summary->{$field->name} ?></strong>
                            <?php endif ?>
                        </td>
                    <?php endforeach ?>
                    <td class="printhide"></td>
                </tr>
            <?php endif ?>

            <?php if (@$groupfield && @$record->{$groupfield} != $lastgroup): ?>
                <?php
                    if (!$seen_today && strcmp($currentgroup, $record->{$groupfield}) < 0) {
                        unset($record);

                        $record = (object) [$groupfield => $currentgroup];
                        $i--;
                        $skip = true;
                    }
                ?>

                <?php if (@$record->{$groupfield}): ?>
                    <?php if ($i > 0): ?>
                        </tbody>
                        <tbody>
                    <?php endif ?>

                    <tr class="<?= strcmp($record->{$groupfield}, @$currentgroup) ? '' : 'today' ?>">
                        <td class="when-selecting printhide"><i class="icon icon--smallsquare-o selectall"></i></td>
                        <?php
                            $grouptitle = $record->{$groupfield};
                            if ($daterange) {
                                $grouphref = strtok($_SERVER['REQUEST_URI'], '?') . '?' . $daterange->constructQuery(['period' => 'day', 'rawrawfrom' => $record->{$groupfield}]) . '&back=' . base64_encode($_SERVER['REQUEST_URI']);
                                $grouptitle = "<a class=\"incog\" href=\"{$grouphref}\">" . $grouptitle . "</a>";
                            }
                        ?>
                        <td class="sacrifice" colspan="<?= $num_cols ?>" style="line-height: 2em; font-weight: bold"><?= $grouptitle ?></td>
                        <td class="nsacrifice first-child" colspan="<?= $num_nonsacrifice_cols ?>" style="line-height: 2em; font-weight: bold"><?= $grouptitle ?></td>
                        <td class="printhide" style="text-align: right; color: black; position: relative; vertical-align: middle">
                            <?php if (!@$hideadd && count(@$types ?: []) > 0): ?>
                                <?php if (count($types) > 1): ?>
                                    <div class="inline-modal inline-modal--right">
                                        <nav>
                                            <?php foreach ($types as $_type): ?>
                                                <a href="<?= addlink($_type, @$record->{$groupfield}, @$groupfield, @$defaultgroup, @$parent_query, $prepop) ?>"><i class="icon icon--mono icon--<?= Linetype::load(AUTH_TOKEN, $_type)->icon ?>"></i></a>
                                            <?php endforeach ?>
                                        </nav>
                                    </div>
                                    <a class="inline-modal-trigger"><i class="icon icon--mono icon--plus"></i></a>
                                <?php else: ?>
                                    <a href="<?= addlink($types[0], @$record->{$groupfield}, @$groupfield, @$defaultgroup, @$parent_query, @$prepop) ?>"><i class="icon icon--mono icon--plus"></i></a>
                                <?php endif ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endif ?>
            <?php endif ?>

            <?php if (!@$skip): ?>
                <tr
                    <?= @$parent ? "data-parent=\"{$parent}\"" : '' ?>
                    <?php if (@$groupfield): ?>data-group="<?= @$record->{$groupfield} ?>"<?php endif ?>
                    class="<?php foreach (@$classes ?: [] as $_class) { echo @$record->{"{$_class->name}"} . ' '; } ?>"
                    data-id="<?= $record->id ?>"
                    data-type="<?= $record->type ?>"
                >
                    <td class="when-selecting printhide"><input type="checkbox"></td>
                    <?php foreach ($fields as $field): ?>
                        <?php $value = @$field->value ? computed_field_value($record, $field->value) : @$record->{$field->name}; ?>
                        <td data-value="<?= $value ?>" style="<?= $field->type == "number" ? 'text-align: right;' : '' ?>" class="<?= @$field->sacrifice ? 'sacrifice' : '' ?>"><?php
                            if (@$field->customlink) {
                                ?><a class="incog" href="<?= is_string($field->customlink) ? computed_field_value($record, $field->customlink) : $record->{"{$field->name}_link"} ?>" <?= @$field->download ? 'download' : '' ?>><?php
                            }

                            if ($field->type == 'icon') {
                                ?><i class="icon icon--mono icon--<?= @$field->translate->{$value} ?? $value ?>"></i><?php
                            } elseif ($field->type == 'color') {
                                ?><span style="display: inline-block; height: 1em; width: 1em; background-color: #<?= $value ?>;">&nbsp;</span><?php
                            } elseif ($field->type == 'file' && @$record->{"{$field->name}_path"}) {
                               ?><a href="/download/<?= $record->{"{$field->name}_path"} ?>" download><i class="icon icon--mono icon--<?= @$field->translate[$field->icon] ?? $field->icon ?>"></i></a><?php
                            } else {
                                echo is_callable(@$field->prefix) ? ($field->prefix)($record) : @$field->prefix;
                                echo $field->type == 'fake' ? $field->value : (strlen($value) > MAX_COLUMN_WIDTH ? substr($value, 0, MAX_COLUMN_WIDTH - 1) . "&hellip;" : $value);
                                echo is_callable(@$field->suffix) ? ($field->suffix)($record) : @$field->suffix;
                            }

                            if (@$field->customlink) {
                                ?></a><?php
                            }
                        ?></td>
                    <?php endforeach ?>
                    <td class="printhide" style="text-align: right; vertical-align: middle">
                        <a href="<?= editlink($record->id, $record->type) ?>"><i class="icon icon--edit"></i></a>
                        <?php if (@$parent): ?>
                            <i class="trigger-unlink-line icon icon--unlink"></i>
                        <?php endif ?>
                        <i class="trigger-delete-line icon icon--times"></i>
                    </td>
                </tr>
            <?php endif ?>

            <?php
                if (@$groupfield) {
                    $lastgroup = @$record->{$groupfield};
                    $seen_today |= (strcmp($lastgroup, date('Y-m-d')) == 0);
                }
            ?>
        <?php endfor ?>
    </tbody>
</table>

<nav>
    <?php foreach ($types as $_type): ?>
       <a href="<?= addlink($_type, @$defaultgroup, @$groupfield, @$defaultgroup, @$parent_query, @$prepop) ?>"><i class="icon icon--mono icon--plus"></i> <i class="icon icon--mono icon--<?= Linetype::load(AUTH_TOKEN, $_type)->icon ?>"></i></a>
    <?php endforeach ?>
</nav>
