<!DOCTYPE html>
<html lang="en-NZ">
    <head>
        <meta name="viewport" content="width=320, initial-scale=1, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="/css/styles.<?= latest('css') ?>.css">
        <meta charset="utf-8"/>
        <title><?= Config::get()->instance_name ?: 'Blends' ?></title>
        <style>
            .appcolor-bg,
            .button.button--main,
            nav a.current,
            td.today,
            tr.today td,
            .periodchoice.periodchoice--current,
            .nav-dropdown a.current,
            .drnav.current {
                background-color: #<?= HIGHLIGHT ?>;
            }

            .button.button--main {
                border: 1px solid #<?= adjustBrightness(HIGHLIGHT, -60) ?>
            }

        </style>
</head>
<body class="wsidebar">
    <?php require APP_HOME . '/src/php/partial/nav.php'; ?>
    <?php if (@$GLOBALS['title']): ?>
        <h3><?= $GLOBALS['title'] ?></h3>
    <?php endif ?>
    <?php require APP_HOME . '/src/php/partial/content/' . PAGE . '.php'; ?>
    <?php
        foreach (ContextVariableSet::getAll() as $active) {
            $active->enddisplay();
        }
    ?>

    <script>
        <?php $username = Blends::token_username($_SESSION['AUTH']); ?>
        <?= "window.username = '{$username}';"; ?>

        <?php $user = Blends::token_user($_SESSION['AUTH']); ?>
        <?= 'window.user = ' . ($user ? "'{$user}'" : 'null') . ";"; ?>

        <?php foreach (PAGE_PARAMS as $key => $value): ?>
            <?= "window.{$key} = '{$value}';"; ?>

        <?php endforeach ?>

        <?php if (BACK): ?>
            <?= "var back = '" . BACK . "';"; ?>

        <?php endif ?>
    </script>
    <?php @include APP_HOME . '/src/php/partial/js/' . PAGE . '.php'; ?>
    <script type="text/javascript" src="/js/app.<?= latest('js') ?>.js"></script>
    <!-- <i class="icon icon--tick" style="position: fixed; top: 0.5em; right: 0.5em; z-index: 99999999999"></i> -->
</body>
</html>
