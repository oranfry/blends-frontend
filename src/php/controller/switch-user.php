<?php
list($user) = Blend::load($_SESSION['AUTH'], 'users')->search($_SESSION['AUTH'], [
    (object) ['field' => 'username', 'cmp' => '=', 'value' => $_POST['username']],
]);

list($token) = Blend::load($_SESSION['AUTH'], 'tokens')->search($_SESSION['AUTH'], [
    (object) ['field' => 'token', 'cmp' => '=', 'value' => $_SESSION['AUTH']],
]);

$token->user = $user->id;

list($token) = Linetype::load($_SESSION['AUTH'], 'token')->save($_SESSION['AUTH'], [$token]);

header("Location: /");
die();