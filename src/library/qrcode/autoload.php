<?php

use cleanphp\base\Variables;

spl_autoload_register(function ($class) {
    $class = str_replace('MGQrCodeReader\\', 'MGQrCodeReader' . DS, $class);
    $file = Variables::getLibPath($class . '.php');

    if (file_exists($file)) include_once $file;

}, true, true);
