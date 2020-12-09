<?php
namespace contextvariableset;

use Config;
use Period;
use ContextVariableSet;
use BlendsConfig;

class Daterange extends \ContextVariableSet
{
    public $period;
    public $rawrawfrom;
    public $rawto;
    public $from;
    public $to;

    public function __construct($prefix)
    {
        parent::__construct($prefix);

        $data = $this->getRawData();
        $this->period = @$data['period'] ?: BlendsConfig::get(AUTH_TOKEN)->periods[0];
        $current_period = Period::load($this->period);

        if (@$data['rawto']) {
            $this->rawrawfrom = $data['rawrawfrom'];
            $this->rawto = $data['rawto'];
            $this->from = $data['rawrawfrom'];
            $this->to = $data['rawto'];
        } else {
            $this->rawrawfrom = @$data['rawrawfrom'] ?: date('Y-m-d');
            $rawfrom = $current_period->rawstart($this->rawrawfrom);
            $this->from = $current_period->start($rawfrom);

            if ($current_period->step) {
                $this->to = date_shift($current_period->start(date_shift($rawfrom, "+{$current_period->step}")), '-1 day');
            }
        }
    }

    public function display()
    {
        $current_period = Period::load($this->period);
        $highlight = ['', '', ''];

        if (@$current_period->suppress_nav) {
            $daterangetitle = $current_period->navlabel;
        } else {
            $rawprevfrom = $current_period->rawstart(date_shift($this->from, "-{$current_period->step}"));
            $rawcurrfrom = $current_period->rawstart(date('Y-m-d'));
            $rawnextfrom = $current_period->rawstart(date_shift($this->from, "+{$current_period->step}"));

            $prevfrom = $current_period->start($rawprevfrom);
            $currfrom = $current_period->start($rawcurrfrom);
            $nextfrom = $current_period->start($rawnextfrom);

            $prevto = date_shift($current_period->start(date_shift($rawprevfrom, "+{$current_period->step}")), '-1 day');
            $currto = date_shift($current_period->start(date_shift($rawcurrfrom, "+{$current_period->step}")), '-1 day');
            $nextto = date_shift($current_period->start(date_shift($rawnextfrom, "+{$current_period->step}")), '-1 day');

            $inuse = ($this->from == date_shift($prevto, '+1 day') && $this->to == date_shift($nextfrom, '-1 day'));

            if ($inuse) {
                $highlight[min(1, max(-1, strcmp($this->from, $currfrom))) + 1] = 'current';
            }

            $daterangetitle = $inuse ?
                $current_period->label($this->from, $this->to) :
                date('D j F Y', strtotime($this->from)) . ' ~ ' . date('D j F Y', strtotime($this->to));
        } ?>
            <div class="navset">
                <div class="inline-rel">
                    <div class="inline-modal">
                        <div class="nav-dropdown nav-dropdown--always">
                             <?php foreach (BlendsConfig::get(AUTH_TOKEN)->periods as $period): ?>
                                <?php $current = (!$this->rawto && $period == $this->period); ?>
                                <a class="<?= $current ? 'current' : '' ?>" href="<?= strtok($_SERVER['REQUEST_URI'], '?') . '?' . $this->constructQuery(['period' => $period, 'rawto' => null]); ?>"><?= Period::load($period)->navlabel ?></a>
                            <?php endforeach ?>
                            <?php if (!@$current_period->suppress_custom): ?>
                                <a class="open-custom-daterange <?= $this->rawto ? 'current' : '' ?>">Custom</a>
                            <?php endif ?>
                            <div class="standard-daterange" style="<?= $this->rawto ? 'display: none' : '' ?>; margin-bottom: 0.5em">
                                <input class="cv-surrogate cv-manip" data-for="<?= $this->prefix ?>__rawrawfrom" type="text" value="<?= $this->from ?>" data-manips="<?= $this->prefix ?>__rawto=" style="width: 6em"><br>
                            </div>
                            <div class="custom-daterange" style="<?= $this->rawto ? '' : 'display: none' ?>">
                                <input class="cv-surrogate cv-manip no-autosubmit" data-for="<?= $this->prefix ?>__rawrawfrom" type="text" value="<?= $this->from ?>" data-manips="<?= $this->prefix ?>__rawto=<?= $this->to ?>" style="width: 6em"><br>
                                to<br>
                                <input class="cv-surrogate no-autosubmit" data-for="<?= $this->prefix ?>__rawto" type="text" value="<?= $this->rawto ?: $this->to ?>" style="width: 6em"><br>
                                <a class="cv-manip" data-manips="">Apply</a>
                            </div>
                        </div>
                    </div>
                    <a class="inline-modal-trigger"><?= strtoupper($this->rawto ? 'c' : $current_period->id) ?></a>
                    <?php if (!@$current_period->suppress_nav): ?>
                        <div class="drnav <?= $highlight[0] ?>"><a class="icon icon--arrowleft" href="<?= strtok($_SERVER['REQUEST_URI'], '?') . '?' . $this->constructQuery(['rawrawfrom'=> $prevfrom]); ?>"></a></div>
                        <div class="drnav <?= $highlight[1] ?>"><a class="icon icon--dot" href="<?= strtok($_SERVER['REQUEST_URI'], '?') . '?' . $this->constructQuery(['rawrawfrom'=> $currfrom]); ?>"></a></div>
                        <div class="drnav <?= $highlight[2] ?>"><a class="icon icon--arrowright" href="<?= strtok($_SERVER['REQUEST_URI'], '?') . '?' . $this->constructQuery(['rawrawfrom'=> $nextfrom]); ?>"></a></div>
                    <?php endif ?>
                </div>
                <input class="cv" type="hidden" name="<?= $this->prefix ?>__period" value="<?= $this->period ?>">
                <input class="cv" type="hidden" name="<?= $this->prefix ?>__rawrawfrom" value="<?= $this->rawrawfrom ?>">
                <input class="cv" type="hidden" name="<?= $this->prefix ?>__rawto" value="<?= $this->rawto ?>">
            </div>
        <?php

        $GLOBALS['title'] = (@$GLOBALS['title'] ? @$GLOBALS['title'] . ' ' : '') . $daterangetitle;
    }
}
