<div class="navbar-placeholder" style="height: 2.5em;">&nbsp;</div>
<div class="instances navbar printhide">
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

                                foreach ($nav as $key => $value) {
                                    if (is_string($key)) {
                                        $blend = $blend_lookup[$value[0]];

                                        if (!$blend) {
                                            error_response('No such blend ' . $value[0]);
                                        }

                                        if (in_array(BLEND_NAME, $value)) {
                                            $current_navgroup = $key;
                                            $current_navgroup_items = $value;
                                            $ddlabel = $current_navgroup;
                                        }

                                        $current = $current_navgroup == $key;
                                        $label = $key;
                                    } else {
                                        $blend = $blend_lookup[$value];

                                        if (!$blend) {
                                            error_response('No such blend ' . $value);
                                        }

                                        if ($blend->name == BLEND_NAME) {
                                            $ddlabel = @$blend->label ?? $blend->name;
                                        }

                                        $current = $blend->name == BLEND_NAME;
                                        $label = @$blend->label ?? $blend->name;
                                    }
                                    ?><a href="/blend/<?= $blend->name ?><?= $query ?>" <?= $current ? 'class="current"' : '' ?>><?= $label ?></a><?php
                                }
                            ?>
                        </div>
                    </div>
                    <span class="inline-modal-trigger"><?= $ddlabel ?></span>
                </div>

                <?php if (count($current_navgroup_items) > 1): ?>
                    <div class="navset">
                        <div class="inline-modal">
                            <div class="nav-dropdown">
                                <?php
                                    foreach ($current_navgroup_items as $value) {
                                        $blend = $blend_lookup[$value];

                                        if (!$blend) {
                                            error_response('No such blend ' . $value);
                                        }

                                        ?><a href="/blend/<?= $blend->name ?><?= $query ?>" <?= $blend->name == BLEND_NAME ? 'class="current"' : '' ?>><?= @$blend->label ?? $blend->name ?></a><?php
                                    }
                                ?>
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
        <div class="navset"><i class="icon icon--leave trigger-logout"></i></div>
        <input type="hidden" name="_returnurl" value="<?= htmlspecialchars_decode($_SERVER['REQUEST_URI']) ?>">
        <div id="new-vars-here" style="display: none"></div>
    </div>
</div>
