<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * File replace.php
 * Created By ankio.
 * Date : 2023/5/26
 * Time : 22:07
 * Description :
 */
//重写nginx缓存
file_put_contents($argv[1],str_replace(<<<EOF
location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
        error_log /dev/null;
        access_log /dev/null;
    }

    location ~ .*\.(js|css)?$
    {
        expires      12h;
        error_log /dev/null;
        access_log /dev/null;
    }
EOF,
'',file_get_contents($argv[1])
));
$dir = __DIR__."/app/storage/cache/cleanphp/";
mkdir($dir,0777,true);
/*file_put_contents($dir."/install.lock","b:1;");

$config =  __DIR__."/app/config_example.php";
$username = "admin";
$password = password_hash($username . "123456",PASSWORD_DEFAULT);
file_put_contents($config,
    str_replace([
        "LOGIN_USERNAME",
        "LOGIN_PASSWORD"
    ],[
        $username,
        $password
    ],file_get_contents($config)));*/
