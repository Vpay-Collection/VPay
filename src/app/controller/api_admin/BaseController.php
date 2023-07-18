<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\admin
 * Class BaseController
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 17:28
 * Description :
 */

namespace app\controller\api_admin;

use cleanphp\base\Controller;
use cleanphp\base\Variables;
use cleanphp\engine\EngineManager;
use library\login\LoginManager;

class BaseController extends Controller
{
    public function __init(): ?string
    {

        if (!LoginManager::init()->isLogin()) {
            return EngineManager::getEngine()->render(403, null, LoginManager::init()->getLoginUrl());
        }
        return null;
    }

    public function json($code = 200, $msg = "OK", $data = null, $count = 0): string
    {
        return EngineManager::getEngine()->render($code, $msg, $data, $count);
    }

    function log(): string
    {
        $dirs = scandir(Variables::getStoragePath('logs'));
        $dirs[] = date('Y-m-d') . DS . 'cleanphp.log';
        $array = [];
        foreach ($dirs as $dir) {
            $new = Variables::getStoragePath('logs', $dir);

            if (is_file($new)) {
                $file_handle = fopen($new, "r");
                if ($file_handle) {
                    while (!feof($file_handle)) { //判断是否到最后一行
                        $line = fgets($file_handle, 4096); //读取一行文本
                        if (str_contains($line,"[ AppChannel ]")) {
                            $array[] = trim($line);
                        }
                        if (sizeof($array) > 500) break;//最多读500行日志
                    }
                }
                fclose($file_handle);//关闭文件
            }
        }
        if (empty($array)) return $this->json(404, "无日志");
        return $this->json(200, null, $array);
    }
}