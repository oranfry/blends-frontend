<?php if (@$options): ?>
    <select name="<?= $field->name ?>" style="width: 80%" tabindex="1">
        <?php if (!@$field->constrained || count($options) > 1): ?><option></option><?php endif ?>
        <?php foreach ($options as $k => $v): ?>
            <?php
                $_value = $v;
                $_label = is_numeric($k) ? $v : $k;
            ?>
            <option <?= $_value == @$value ? 'selected="selected"' : '' ?> value="<?= $_value ?>"><?= $_label ?></option>
        <?php endforeach ?>
    </select>
    <?php if (!@$field->constrained): ?>
        <button type="button" class="adhoc-toggle">&hellip;</button>
    <?php endif ?>
<?php else: ?>
    <input class="field value" type="text" name="<?= $field->name ?>" value="<?= $value ?>" autocomplete="off">
<?php endif ?>

