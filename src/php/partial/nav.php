<?php use contextvariableset\Hidden; ?>
<?php use contextvariableset\Repeater; ?>
<div class="navbar-placeholder" style="height: 2.5em;">&nbsp;</div>
<div class="instances navbar printhide">
    <form id="instanceform">
        <div>
            <?php if (BACK): ?><div class="navset sidebar-backlink-container"><a class="sidebar-backlink" href="<?= BACK ?>">Back</a></div><?php endif ?>
            <?php if (defined('BLEND_NAME')): ?>
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
            <?php if (AUTH_TOKEN): ?>
                <div class="navset">
                    <div class="nav-title">Logout</div>
                    <i class="icon icon--leave trigger-logout" title="Logout <?= Blends::token_username(AUTH_TOKEN) ?>"></i>
                </div>
            <?php endif ?>
            <input type="hidden" name="_returnurl" value="<?= htmlspecialchars_decode($_SERVER['REQUEST_URI']) ?>">
            <div id="new-vars-here" style="display: none"></div>
        </div>
    </form>
    <form id="tokenform" action="/change-token" method="post" class="only-super1200">
        <div class="navset">
            <div class="nav-title">Token</div>
            <input type="text" name="token" value="<?= AUTH_TOKEN ?>" style="width: 100%; padding: 0.5em">
        </div>
    </form>
    <?php if (defined('ROOT_USERNAME') && AUTH_TOKEN && Blends::token_username(AUTH_TOKEN) == ROOT_USERNAME): ?>
        <form id="tokenform" action="/switch-user" method="post" class="only-super1200">
            <div class="navset">
                <div class="nav-title">Switch User</div>
                <input type="text" name="username" value="<?= ROOT_USERNAME ?>" style="width: 100%; padding: 0.5em">
            </div>
        </form>
    <?php endif ?>
</div>
