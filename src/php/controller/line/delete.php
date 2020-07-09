<?php
define('LAYOUT', 'json');

$linetype = Linetype::load(LINETYPE_NAME);

$result = $linetype->delete($_SESSION['AUTH'], [(object) [
    'field' => 'id',
    'value' => LINE_ID,
]]);
