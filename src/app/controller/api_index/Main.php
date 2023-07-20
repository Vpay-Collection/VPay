<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
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
use cleanphp\base\Response;
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

Disallow: /
EOF;
    }

    function image()
    {
        $file = get('file', '');
        Route::renderStatic(Variables::getStoragePath("uploads", get("type", "temp"), $file));
    }
    private function isDocker(): bool
    {
        return file_exists(APP_DIR.DS."docker_runtime");
    }
    function install(): string
    {
        $cache = Cache::init(0,Variables::getCachePath('cleanphp',DS));
        if (!empty($cache->get("install.lock"))) {
            return $this->render(403);
        }
        $isDocker = $this->isDocker();
        $servername = $isDocker?"0.0.0.0":arg('host');
        $port = $isDocker?3306:arg('port',3306); // MySQL 默认端口是 3306
        $username = $isDocker?"":arg('username');
        $password = $isDocker?"":arg('password');
        $database = $isDocker?"test":arg('database');
        try {
            $conn = new PDO("mysql:host=127.0.0.1;port=$port;dbname=$database", $username, $password);
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


        if(empty($_password)||empty($_username)){
            return EngineManager::getEngine()->render(502,"用户名或密码不允许为空");
        }

        $encrypt = password_hash($_username . $_password,PASSWORD_DEFAULT);

        Config::setConfig('login',[
            'username' => $_username,
            'password' => $encrypt,
            'image' => Request::getAddress()."/clean_static/img/head.png",
        ]);

        $frame = Config::getConfig("frame");
        $frame['host'][0] = arg('domain');
        Config::setConfig("frame",$frame);
        $cache->set("install.lock",true);
        return $this->render(200);
    }
}