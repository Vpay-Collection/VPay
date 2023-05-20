<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * Package: app\controller\api_index
 * Class Main
 * Created By ankio.
 * Date : 2023/5/18
 * Time : 17:56
 * Description :
 */

namespace app\controller\api_index;

use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Request;
use cleanphp\base\Route;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;
use PDO;
use PDOException;

class Main extends Controller
{
    function robots(): string
    {
        return <<<EOF
User-agent: *

Disallow: /*
EOF;
    }

    function image()
    {
        $file = get('file', '');
        Route::renderStatic(Variables::getStoragePath("uploads", get("type", "temp"), $file));
    }

    function install()
    {

        $servername = arg('host');
        $port = arg('port',3306); // MySQL 默认端口是 3306
        $username = arg('username');
        $password = arg('password');
        $database = arg('database');
        try {
            $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password);
        } catch (PDOException $e) {
           return EngineManager::getEngine()->render(403,$e->getMessage());
        }

        Config::setConfig('database',[
            'main'=>[
                'type' => 'mysql',
                'host' => $servername,
                'username' => $username,
                'password' => $password,
                'port' => $port,
                'db' => $database,
                'charset' => 'utf8mb4',
            ]
        ]);

        $_username = arg('_username');
        $_password = arg('_password');

        $encrypt = password_hash($_username . $_password,PASSWORD_DEFAULT);

        Config::setConfig('login',[
            'username' => $_username,
            'password' => $encrypt,
            'image' => Request::getAddress()."/clean_static/img/head.jpg",
        ]);

        $frame = Config::getConfig("frame");
        $frame['host'][0] = arg('domain');
        Config::setConfig("frame",$frame);
        Cache::init()->set("install",true);
        return $this->render(200);
    }
}