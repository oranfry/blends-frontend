<?php
Blends::logout(AUTH_TOKEN);
unset($_SESSION['AUTH']);
header("Location: /");
die('Redirecting...');
