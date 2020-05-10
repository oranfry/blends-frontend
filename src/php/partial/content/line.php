<h2><?= @$linetype->label ?: ucwords($linetype->name) ?></h2>

<div class="samewidth">
    <div class="line">
        <form method="post" class="edit-form" <?= $hasFileFields ? 'enctype="multipart/form-data"' : '' ?>>
            <?php
                foreach ($linetype->find_incoming_links() as $parent) {
                    $tablelink = Tablelink::load($parent->parent_link);
                    $side = @$parent->reverse ? 1 : 0;

                    $field = (object) ['name' => $tablelink->ids[$side]];
                    $value = @$line->{$field->name} ?: @$_GET[$field->name] ?: @$field->default;

                    $options = [];
                    if (@$line->{$tablelink->ids[$side]}) {
                        $options[] = $line->{$tablelink->ids[$side]};
                    }
                    ?>
                    <div class="form-row">
                        <div class="form-row__label"><?= $tablelink->ids[$side] ?></div>
                        <div class="form-row__value">
                            <?php require APP_HOME . "/src/php/partial/fieldtype/text.php"; ?>
                        </div>
                        <div style="clear: both"></div>
                    </div>
                        <?php
                }

                foreach ($linetype->fields as $field) {
                    if (@$field->derived && !@$field->show_derived) {
                        continue;
                    }

                    if ($field->type == 'file') {
                        $value = @$line->{"{$field->name}_path"};
                    } else {
                        $value = @$line->{$field->name} ?: @$_GET[$field->name] ?: @$field->default;
                    }
                    $options = @$suggested_values[$field->name];

                    if ($value && $options && !in_array($value, $options)) {
                        array_unshift($options, $value);
                    } ?>
                    <div class="form-row">
                        <div class="form-row__label"><?= @$field->label ?? $field->name ?></div>
                        <div class="form-row__value">
                            <?php require APP_HOME . "/src/php/partial/fieldtype/{$field->type}.php"; ?>
                        </div>
                        <div style="clear: both"></div>
                    </div>
                    <?php
                }
            ?>

            <div class="form-row">
                <div class="form-row__label">&nbsp;</div>
                <div class="form-row__value">
                    <input class="button button--main" name="action" value="<?= LINE_ID ? 'update' : 'create' ?>" type="submit">
                </div>
                <div style="clear: both"></div>
            </div>
        </form>
    </div>
    <?php
        $parentType = $linetype->name;
        $parentId = $line->id;

        foreach (@$linetype->children ?: [] as $child) {
            $child_linetype = $linetype_lookup[$child->linetype];
            $records = $line->{$child->label};
            $types = [$child_linetype->name];
            $fields = @$child->list_fields ?: $child_linetype->fields;
            $summaries = [$child->label => @$line->{"{$child->label}_summary"} ?: []];
            $tablelink = Tablelink::load($child->parent_link);
            $parent_query = "parentlink={$child->parent_link}&{$tablelink->ids[0]}=" . LINE_ID;
            $parent = "{$child->parent_link}:{$tablelink->ids[0]}=" . LINE_ID;
            $groupfield = 'group';

            foreach ($records as $record) {
                $record->group = $child->label;
            }

            if (property_exists($line, 'date')) {
                $parent_query .= '&date=' . $line->date;
            }

            require APP_HOME . "/src/php/partial/showas/list.php";
        }
    ?>
    <div style="clear: both"></div>
    <?php
    $astext = $linetype->astext($line);
    if ($astext) {
        ?><pre id="printpreview" class="printpreview"><?= $astext ?><button class="print-line">Print</button></pre><br><?php
    }
    ?>
    <pre id="output" style="display: inline-block"></pre><br>
</div>
