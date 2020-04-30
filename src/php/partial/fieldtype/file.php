<span class="file-field-controls">
    <?php if ($value || $bulk): ?>
        <span class="file-field-controls__actions">
            <?php if (!$bulk): ?>
                <a href="/download/<?= $value ?>" download><i class="icon icon--mono icon--<?= @$field->translate[$field->icon] ?? $field->icon ?>"></i></a>
            <?php endif ?>
            <span class="button file-field-controls__change"><?= $bulk ? 'choose' : 'change' ?></span>
            <span class="button file-field-controls__delete">delete</span>
        </span>
    <?php endif ?>
    <span class="file-field-controls__input" <?= $value || $bulk ? 'style="display:none"' : '' ?>>
        <input class="field value" type="file" name="<?= $field->name ?>" style="width: 16em">
        <?php if ($value || $bulk): ?>
            <span class="button file-field-controls__cancel">cancel</span>
        <?php endif ?>
    </span>
    <span class="file-field-controls__willdelete" style="display:none">
        Will delete
        <span class="button file-field-controls__cancel">cancel</span>
    </span>
</span>