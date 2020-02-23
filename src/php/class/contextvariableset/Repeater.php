<?php
namespace contextvariableset;

use \Config;
use \Period;

class Repeater extends \ContextVariableSet
{
    public $period;
    public $n;
    public $pegdate;
    public $day;
    public $month;
    public $round;
    public $offset;
    public $ff;

    public function __construct($prefix)
    {
        parent::__construct($prefix);

        $data = $this->getRawData();

        if (@$data['period']) {
            $this->period = @$data['period'];
            $this->n = @$data['n'] ?: 7;
            $this->pegdate = @$data['pegdate'] ?: date('Y-m-d');
            $this->day = @$data['day'];
            $this->month = @$data['month'];
            $this->round = @$data['round'];
            $this->offset = @$data['offset'];
            $this->ff = @$data['ff'];
        }
    }

    public function display()
    {
        ?>
        <div style="display: none">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__period" value="<?= $this->period ?>">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__n" value="<?= $this->n ?>">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__pegdate" value="<?= $this->pegdate ?>">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__day" value="<?= $this->day ?>">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__month" value="<?= $this->month ?>">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__round" value="<?= $this->round ?>">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__offset" value="<?= $this->offset ?>">
            <input class="cv" type="hidden" name="<?= $this->prefix ?>__ff" value="<?= $this->ff ?>">
        </div>
        <?php
    }

    public function render()
    {
        $r = "{$this->period}:";

        if ($this->period == 'day') {
            $r .= "{$this->pegdate}.{$this->n}";
        } else {
            $r .= "{$this->day}";

            if ($this->period == 'year') {
                $r .= "/{$this->month}";
            }
        }

        if ($this->round !== null) {
            $r .= 'r';
        }

        if ($this->ff) {
            $r .= 'f' . $this->ff;
        }

        if ($this->offset !== null) {
            $r .= str_replace(
                [' ', 'days', 'day', 'weeks', 'week', 'months', 'month', 'years', 'year'],
                ['',  'd',    'd',   'w',     'w',    'm',      'm',      'y',    'y'],
                $this->offset
            );
        }

        return $r;
    }
}
