<?php

with_plugins(function($pdir, $name) use ($latests){
    $dir = "{$pdir}/src/icon";

    if (!is_dir($dir)) {
        return;
    }

    $handle = opendir($dir);

    while ($file = readdir($handle)) {
        if (!preg_match('/(.*)\.png$/', $file, $groups)) {
            continue;
        }

        $icon = $groups[1];

        echo ".icon--{$icon} { background-image: url(/img/icon/{$icon}.{$latests['icon']}.png); }\n";
    }

    closedir($handle);
    
    echo "input[type=\"checkbox\"]:checked { background-image: url(/img/icon/tick.{$latests['icon']}.png); }\n";
});
