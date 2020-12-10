<div class="login-page">
    <h1><?= BlendsConfig::get(AUTH_TOKEN)->instance_name ?: 'Blends' ?></h1>
    <form action="/" method="post">
        <div class="cred-line">
            <p>Username</p>
            <input type="text" name="username" id="auth" autocomplete="off" value="<?= @$username ?>">
        </div>
        <div class="cred-line">
            <p>Password</p>
            <input type="password" name="password" id="password">
        </div>
        <div class="cred-line">
            <input type="submit" value="Sign In">
        </div>

        <?php if (isset($message) && $message): ?>
        <div class="cred-line">
            <p class="error"><?= $message ?></p>
        </div>
        <?php endif ?>
    </form>
</div>
<script>document.getElementById('<?= @$username ? 'password' : 'auth' ?>').focus();</script>
