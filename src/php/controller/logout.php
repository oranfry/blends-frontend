<?php
Blends::logout($_SESSION['AUTH']);
unset($_SESSION['AUTH']);
header("Location: /");
die('Redirecting...');
