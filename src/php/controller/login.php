<?php
$message = null;

if (@$_POST['password'] && @$_POST['username']) {
    $token = Blends::login($_POST['username'], $_POST['password']);

    if ($token) {
        $_SESSION['AUTH'] = $token;
    }
}

if (@$_SESSION['AUTH']) {
    $nav = BlendsConfig::get($_SESSION['AUTH'])->nav;
    $nav0 = array_keys($nav)[0];
    $blend = is_string($nav0) ? $nav[$nav0][0] : $nav[0];

    header("Location: /blend/{$blend}");
    die('Redirecting...');
}

if (isset($_POST['password']) && isset($_POST['username'])) {
    $message = "Incorrect username or password";
}

define('LAYOUT', 'login');

return [
    'message' => $message,
    'username' => @$_POST['username'],
];
