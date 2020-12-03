<?php use contextvariableset\Hidden; ?>
<?php use contextvariableset\Repeater; ?>
<div class="navbar-placeholder" style="height: 2.5em;">&nbsp;</div>
<div class="instances navbar printhide">
    <form id="instanceform">
        <div>
            <?php if (BACK): ?><a class="sidebar-backlink" href="<?= BACK ?>">Back</a></a><?php endif ?>
            <?php if (defined('BLEND_NAME')): ?>
                <?php $nav = @BlendsConfig::get(@$_SESSION['AUTH'])->nav ?? []; ?>
                <?php if (count($nav) > 1): ?>
                    <?php
                        $query = implode('&', array_map(function($v, $k) { return "{$k}={$v}"; }, $_GET, array_keys($_GET)));
                        $query = $query ? '?' . $query : '';
                    ?>
                    <div class="navset">
                        <div class="nav-title">Blends</div>
                        <div class="nav-modal listable">
                            <div class="nav-dropdown">
                                <?php foreach ($blend_lookup as $name => $blend) : ?>
                                    <a href="/blend/<?= $blend->name ?><?= $query ?>" <?= BLEND_NAME == $name ? 'class="current"' : '' ?>><?= $blend->name ?></a>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <span class="nav-modal-trigger only-sub1200"><?= BLEND_NAME ?></span>
                    </div>
                <?php endif ?>

                <?php $mainFilters = ContextVariableSet::getAll(); ?>
                <?php $shownTitle = false; ?>
                <?php if (count($mainFilters)) : ?>
                    <?php foreach ($mainFilters as $active): ?>
                        <?php if (!$active instanceof Hidden && !$active instanceof Repeater && !$shownTitle): ?>
                            <div class="nav-title">Main Filters</div>
                            <?php $shownTitle = true; ?>
                        <?php endif ?>
                        <?php $active->tinydisplay(); ?>
                        <?php $active->display(); ?>
                    <?php endforeach ?>
                <?php endif ?>
            <?php endif ?>
            <?php @include APP_HOME . '/src/php/partial/nav/' . PAGE . '.php'; ?>
            <div class="navset">
                <div class="nav-title">Logout</div>
                <i class="icon icon--leave trigger-logout" title="Logout <?= Blends::token_username($_SESSION['AUTH']) ?>"></i>
            </div>
            <input type="hidden" name="_returnurl" value="<?= htmlspecialchars_decode($_SERVER['REQUEST_URI']) ?>">
            <div id="new-vars-here" style="display: none"></div>
        </div>
    </form>
</div>
