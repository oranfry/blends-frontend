<div class="navbar-placeholder" style="height: 2.5em;">&nbsp;</div>
<div class="instances navbar printhide">
    <form id="instanceform" action="/change-instances" method="post">
        <div>
            <?php if (BACK): ?><a class="sidebar-backlink" href="<?= BACK ?>">Back</a></a><?php endif ?>
            <?php if (defined('BLEND_NAME')): ?>
                <?php if (count($blend_lookup) > 1): ?>
                    <?php
                        $query = implode('&', array_map(function($v, $k) { return "{$k}={$v}"; }, $_GET, array_keys($_GET)));
                        $query = $query ? '?' . $query : '';
                    ?>
                    <div class="navset">
                        <div class="inline-modal">
                            <div class="nav-dropdown">
                                <?php foreach ($blend_lookup as $blend): ?>
                                    <a href="/blend/<?= $blend->name ?><?= $query ?>" <?= $blend->name == BLEND_NAME ? 'class="current"' : ''?>><?= $blend->label ?></a>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <span class="inline-modal-trigger"><?= $blend_lookup[BLEND_NAME]->label ?></span>
                    </div>
                <?php endif ?>
                <?php
                    foreach (ContextVariableSet::getAll() as $active) {
                        $active->tinydisplay();
                        $active->display();
                    }
                ?>
            <?php endif ?>
            <?php @include APP_HOME . '/src/php/partial/nav/' . PAGE . '.php'; ?>
            <input type="hidden" name="_returnurl" value="<?= htmlspecialchars_decode($_SERVER['REQUEST_URI']) ?>">
            <div id="new-vars-here" style="display: none"></div>
        </div>
    </form>
</div>
