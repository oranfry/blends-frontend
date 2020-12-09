<?php
$_SESSION['AUTH'] = $_POST['token'];
$back = $_SESSION['AUTH'] ? @$_SERVER['HTTP_REFERER'] ?? '/' : '/';
header("Location: {$back}");
die();
