<?php
namespace contextvariableset;

class Value extends \ContextVariableSet
{
    public $value;
    public $options;

    public function __construct($prefix)
    {
        parent::__construct($prefix);

        $data = $this->getRawData();

        $this->value = @$data['value'];
    }

    public function display()
    {
        $label = $this->value;

        if ($this->options) {
            $option_lookup = array_flip($this->options);

            if (isset($option_lookup[$this->value]) && is_string($option_lookup[$this->value])) {
                $label = $option_lookup[$this->value];
            }
        }

        $label = $label ?: preg_replace('/.*_/', '', $this->prefix);

        ?><div class="navset">
            <input class="cv" name="<?= $this->prefix ?>__value" placeholder="<?= $this->prefix ?>" value="<?= $this->value ?>" style="display: none">
            <div class="inline-rel">
                <div class="inline-modal">
                    <div class="inline-dropdown">
                        <a class="cv-manip <?= $this->value ? '' : 'current' ?>" data-manips="<?= $this->prefix ?>__value=">-</a>
                        <?php if ($this->options): ?>
                            <?php foreach ($this->options as $index => $option): ?>
                                <a class="cv-manip <?= $this->value == $option ? 'current' : ''?>" data-manips="<?= $this->prefix ?>__value=<?= $option ?>"><?= !is_numeric($index) ? $index : $option ?></a>
                            <?php endforeach ?>
                        <?php else: ?>
                        <?php endif ?>
                    </div>
                </div>
                <span class="inline-modal-trigger"><?= $label ?></span>
            </div>
        </div><?php
    }
}
