<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class BaseController
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 17:28
 * Description :
 */

namespace app\controller\api_admin;

use app\Application;
use core\base\Controller;
use core\base\Response;
use core\base\Variables;
use core\objects\StringBuilder;
use library\database\object\Field;
use library\user\login\Login;

class BaseController extends Controller
{
    public function __init(): ?string
    {
        if (!Login::isLogin()) {
            return Application::json(403, null, "/login");
        }
        return null;
    }

    public function json($code = 200, $msg = "OK", $data = null, $count = 0): string
    {
        return Application::json($code, $msg, $data, $count);
    }

    function log(): string
    {
        $file = arg('file', '');
        if (!Field::isName($file)) return $this->json(404, "无日志");
        $dirs = scandir(Variables::getStoragePath('logs'));
        $dirs[] = date('Y-m-d') . DS . $file . 'cleanphp.log';
        $array = [];
        foreach ($dirs as $dir) {
            $new = Variables::getStoragePath('logs', $dir);

            if (is_file($new)) {
                $file_handle = fopen($new, "r");
                if ($file_handle) {
                    while (!feof($file_handle)) { //判断是否到最后一行
                        $line = fgets($file_handle, 4096); //读取一行文本
                        if ((new StringBuilder($line))->contains("[ $file ]")) {
                            $array[] = trim($line);
                        }
                    }
                }
                fclose($file_handle);//关闭文件
            }
        }
        if (empty($array)) return $this->json(404, "无日志");
        return $this->json(200, null, $array);
    }
}