<?php
$message = null;

if (@$_POST['password'] && @$_POST['username']) {
    // Linetype::load('user')->find_lines([
    //     (object) [
    //         'field' => 'username',
    //         'cmp' => '=',
    //         'value' => $_POST['username'],
    //     ],
    // ]);
    $stmt = Db::prepare("SELECT * from record_user where username = :username and password = SHA2(concat(:password, `salt`), 256)");
    $result = $stmt->execute([
        'username' => @$_POST['username'],
        'password' => @$_POST['password'],
    ]);

    if (!$result) {
        error_response('Login error', 500);
    }

    $user = reset($stmt->fetchAll(PDO::FETCH_ASSOC));

    if ($user) {
        $_SESSION['AUTH'] = true;
    }
}

if (@$_SESSION["AUTH"] === true) {
    $nav = Config::get()->nav;
    $nav0 = array_keys($nav)[0];
    $blend = is_string($nav0) ? $nav[$nav0][0] : $nav[0];

    header("Location: /blend/{$blend}");
    die('Redirecting...');
}

if (@$_POST['password'] && @$_POST['username']) {
    $message = "Incorrect username or password";
}

define('LAYOUT', 'login');

return [
    'message' => $message,
    'username' => @$_POST['username'],
];
