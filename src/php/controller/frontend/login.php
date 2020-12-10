<?php
$message = null;

if (AUTH_TOKEN) {
    error_log('Unexpectedly, token is already present');
    die();
}

if (@$_POST['password'] && @$_POST['username']) {
    $token = Blends::login($_POST['username'], $_POST['password']);

    if ($token) {
        $_SESSION['AUTH'] = $token;
    }
}

if (@$_SESSION['AUTH']) {
    $blends = @BlendsConfig::get($_SESSION['AUTH'])->blends;

    if (!$blends || !count($blends)) {
        error_response('No blends set up');
    }

    $blend = array_keys($blends)[0];

    header("Location: /blend/{$blend}");
    die('Redirecting...');
}

if (isset($_POST['password']) && isset($_POST['username'])) {
    $message = "Incorrect username or password";
}

// define('LAYOUT', 'login');

return [
    'message' => $message,
    'username' => @$_POST['username'],
];
