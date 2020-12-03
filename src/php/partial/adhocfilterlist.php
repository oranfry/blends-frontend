<?php
use contextvariableset\Filter;

if ($adhocfilters->value) {
    foreach (explode(',', $adhocfilters->value) as $filterid) {
        $filter = Filter::get($filterid);

        $manips = [];

        foreach (explode(',', $adhocfilters->value) as $filterid2) {
            if ($filterid != $filterid2) {
                $manips[] = $filterid2;
            }
        } ?><a class="filter cv-manip" data-manips="<?= $adhocfilters->prefix ?>__value=<?= implode(',', $manips) ?>">
            <span><?= $filter->field ?> <?= $filter->cmp ?> <?= $filter->value ?></span>
            <i class="icon icon--times"></i>
        </a><?php
    }
}
