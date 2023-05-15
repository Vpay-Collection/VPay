<?php
/*******************************************************************************
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * File autoload.php
 * Created By ankio.
 * Date : 2023/5/8
 * Time : 11:07
 * Description :
 */
spl_autoload_register(function ($class) {
    $class = str_replace('Ankio\\', 'vpay/', $class);
    $file = __DIR__ . $class . '.php';
    if (file_exists($file)) include_once $file;

}, true, true);