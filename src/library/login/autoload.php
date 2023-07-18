<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * File autoload.php
 * Created By ankio.
 * Date : 2023/5/3
 * Time : 18:10
 * Description :
 */

use cleanphp\base\EventManager;
use cleanphp\base\Variables;
use cleanphp\engine\EngineManager;
use cleanphp\engine\JsonEngine;
use library\login\LoginManager;

EventManager::addListener("__application_init__", function (string $event, &$data) {
    $__module = Variables::get("__request_module__");
    $__controller = Variables::get("__request_controller__");
    $__action = Variables::get("__request_action__");
    if ($__module === 'ankio' && $__controller === 'login') {
        EngineManager::setDefaultEngine(new JsonEngine(["code" => 0, "msg" => "OK", "data" => null, "count" => 0]));
        LoginManager::init()->route($__action);
    }
});