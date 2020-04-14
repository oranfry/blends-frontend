<div class="navbar-placeholder" style="height: 2.5em;">&nbsp;</div>
<div class="instances navbar printhide">
    <form id="instanceform" action="/change-instances" method="post">
        <div>
            <?php if (BACK): ?><a class="sidebar-backlink" href="<?= BACK ?>">Back</a></a><?php endif ?>
            <?php if (defined('BLEND_NAME')): ?>
                <?php $packages = Config::get()->packages; ?>
                <?php $current_package = null; ?>
                <?php if (count($packages) > 1): ?>
                    <?php
                        $query = implode('&', array_map(function($v, $k) { return "{$k}={$v}"; }, $_GET, array_keys($_GET)));
                        $query = $query ? '?' . $query : '';
                    ?>
                    <div class="navset">
                        <div class="inline-modal">
                            <div class="nav-dropdown">
                                <?php foreach ($packages as $alias => $package_params): ?>
                                    <?php $package = Package::rget($alias); ?>
                                    <?php $blend = $blend_lookup[$package->blends[0]]; ?>
                                    <?php
                                        $iscurrentpackage = false;

                                        if (in_array(BLEND_NAME, $package->blends)) {
                                            $current_package_alias = $alias;
                                            $current_package = $package;
                                            $iscurrentpackage = true;
                                        }
                                    ?>
                                    <a href="/<?= $alias ?>/blend/<?= $blend->name ?><?= $query ?>" <?= $iscurrentpackage ? 'class="current"' : ''?>><?= @$package->label ?? $package_params->name ?></a>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <span class="inline-modal-trigger"><?= @$current_package->label ?? $current_package->name ?></span>
                    </div>

                    <?php if (count($current_package->blends) > 1): ?>
                        <div class="navset">
                            <div class="inline-modal">
                                <div class="nav-dropdown">
                                    <?php foreach ($current_package->blends as $blend_name): ?>
                                        <?php $blend = $blend_lookup[$blend_name]; ?>
                                        <a href="/<?= $current_package_alias ?>/blend/<?= $blend->name ?><?= $query ?>" <?= $blend->name == BLEND_NAME ? 'class="current"' : ''?>><?= @$blend->label ?? $blend_name ?></a>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <span class="inline-modal-trigger"><?= $blend_lookup[BLEND_NAME]->label ?></span>
                        </div>
                    <?php endif ?>
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
