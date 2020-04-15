<div class="navbar-placeholder" style="height: 2.5em;">&nbsp;</div>
<div class="instances navbar printhide">
    <form id="instanceform" action="/change-instances" method="post">
        <div>
            <?php if (BACK): ?><a class="sidebar-backlink" href="<?= BACK ?>">Back</a></a><?php endif ?>
            <?php if (defined('BLEND_NAME')): ?>
                <?php $nav = @Config::get()->nav ?? []; ?>
                <?php if (count($nav) > 1): ?>
                    <?php
                        $query = implode('&', array_map(function($v, $k) { return "{$k}={$v}"; }, $_GET, array_keys($_GET)));
                        $query = $query ? '?' . $query : '';
                    ?>
                    <div class="navset">
                        <div class="inline-modal">
                            <div class="nav-dropdown">
                                <?php
                                    $ddlabel = null;
                                    $current_navgroup = null;
                                    $current_navgroup_items = [];
                                ?>
                                <?php foreach ($nav as $key => $value): ?>
                                    <?php if (is_string($key)): ?>
                                        <?php $blend = $blend_lookup[$value[0]]; ?>
                                        <?php if (in_array(BLEND_NAME, $value)) { $current_navgroup = $key; $current_navgroup_items = $value; $ddlabel = $current_navgroup; } ?>
                                        <a href="/blend/<?= $blend->name ?><?= $query ?>" <?= $current_navgroup == $key ? 'class="current"' : '' ?>><?= $key ?></a>
                                    <?php else: ?>
                                        <?php $blend = $blend_lookup[$value]; ?>
                                        <?php if ($blend->name == BLEND_NAME) { $ddlabel = @$blend->label ?? $blend->name; } ?>
                                        <a href="/blend/<?= $blend->name ?><?= $query ?>" <?= $blend->name == BLEND_NAME ? 'class="current"' : '' ?>><?= @$blend->label ?? $blend->name ?></a>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <span class="inline-modal-trigger"><?= $ddlabel ?></span>
                    </div>

                    <?php if (count($current_navgroup_items) > 1): ?>
                        <div class="navset">
                            <div class="inline-modal">
                                <div class="nav-dropdown">
                                    <?php foreach ($current_navgroup_items as $value): ?>
                                        <?php $blend = $blend_lookup[$value]; ?>
                                        <a href="/blend/<?= $blend->name ?><?= $query ?>" <?= $blend->name == BLEND_NAME ? 'class="current"' : '' ?>><?= @$blend->label ?? $blend->name ?></a>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <span class="inline-modal-trigger"><?= @Blend::load(BLEND_NAME)->label ?? BLEND_NAME ?></span>
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
