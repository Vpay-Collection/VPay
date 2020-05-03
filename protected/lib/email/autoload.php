<?php
spl_autoload_register(function($class)
{
    $class = str_replace('PHPMailer\\PHPMailer\\','email/', $class);
    $file =APP_LIB .  $class . '.php';
    //echo $file.'<br >';
    if (file_exists($file)) include $file;

}, true, true);
