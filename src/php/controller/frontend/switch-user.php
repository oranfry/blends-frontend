<?php
list($user) = Blend::load(AUTH_TOKEN, 'users')->search(AUTH_TOKEN, [
    (object) ['field' => 'username', 'cmp' => '=', 'value' => $_POST['username']],
]);

list($token) = Blend::load(AUTH_TOKEN, 'tokens')->search(AUTH_TOKEN, [
    (object) ['field' => 'token', 'cmp' => '=', 'value' => AUTH_TOKEN],
]);

$token->user = $user->id;

list($token) = Linetype::load(AUTH_TOKEN, 'token')->save(AUTH_TOKEN, [$token]);

header("Location: /");
die();