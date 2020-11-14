<!DOCTYPE html>
<html style="height: 100%; text-align: center; margin: 0">
<head>
    <title><?= BlendsConfig::get(@$_SESSION['AUTH'])->instance_name ?: 'Blends' ?></title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" href="/icon.png">
    <link rel="stylesheet" type="text/css" href="/css/styles.<?= latest('css') ?>.css">
</head>

<body class="login-page">
    <div class="middle">
        <div class="middle-inner" style="position: relative">
            <div class="cols">
                <div class="col">
                    <h1 class="mobile-only"><?= BlendsConfig::get(@$_SESSION['AUTH'])->instance_name ?: 'Blends' ?></h1>
                    <div style="display: inline-block; background: url(<?= @BlendsConfig::get(@$_SESSION['AUTH'])->logopath ?>) center center; width: 200px; height: 200px; background-size: cover"></div>
                </div>
                <div class="col">
                    <form action="" method="post">
                        <div style="display: inline-block; text-align: left; margin: 1em 0">
                        <h1 class="desktop-only"><?= BlendsConfig::get(@$_SESSION['AUTH'])->instance_name ?: 'Blends' ?></h1>
                            <div class="cred-line">
                                <p>Username</p>
                                <input type="text" name="username" id="auth" autocomplete="off" value="<?= @$username ?>">
                            </div>
                            <div class="cred-line">
                                <p>Password</p>
                                <input type="password" name="password">
                            </div>
                            <div class="cred-line">
                                <input type="submit" value="Sign In">
                            </div>

                            <?php if (isset($message) && $message): ?>
                            <div class="cred-line">
                                <p class="error"><?= $message ?></p>
                            </div>
                            <?php endif ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>document.getElementById('auth').focus();</script>
</body>
</html>
