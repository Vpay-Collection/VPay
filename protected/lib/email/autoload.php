<?php
spl_autoload_register('autoload', true, true);
function autoload($class)
{
    $class = str_replace('PHPMailer\\PHPMailer\\','email/', $class);
    $file =APP_LIB .  $class . '.php';
    //echo $file.'<br >';
    if (file_exists($file)) include $file;

}