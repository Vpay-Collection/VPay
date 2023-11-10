<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\controller\index
 * Class Install
 * Created By ankio.
 * Date : 2023/5/19
 * Time : 12:20
 * Description :
 */

namespace app\controller\index;

use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Request;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;
use PDO;
use PDOException;

class Install extends Controller
{
    private Cache $cache;
    public function __init()
    {
        $this->cache = Cache::init(0,Variables::getCachePath('cleanphp',DS));
    }

    function info()
    {

        $install = !empty( $this->cache->get("install.lock"));

        return $this->render(200, null, [
            'install' => $install,
            'docker' => $this->isDocker(),
            'build'=> 'Version '.Config::getConfig('frame')['version']
        ]);
    }

    private function isDocker()
    {
        return getenv('DOCKER_CONTAINER');
    }

    function start(): string
    {
        if (!empty( $this->cache->get("install.lock"))) {
            return $this->render(403);
        }
        $isDocker = $this->isDocker();
        $servername = $isDocker?"127.0.0.1":arg('host');
        $port = $isDocker?3306:arg('port',3306); // MySQL 默认端口是 3306
        $username = $isDocker?"":arg('username');
        $password = $isDocker?"":arg('password');
        $database = $isDocker?"test":arg('database');
        try {
            $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password);
            unset($conn);
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
            return EngineManager::getEngine()->render(403,"用户名或密码不允许为空");
        }

        $encrypt = password_hash($_username . $_password,PASSWORD_DEFAULT);

        Config::setConfig('login',[
            'username' => $_username,
            'password' => $encrypt,
            'image' => "img/head.png",
        ]);

        $frame = Config::getConfig("frame");
        $frame['host'][0] = arg('domain');
        Config::setConfig("frame",$frame);
        $this->cache->set("install.lock",true);
        return $this->render(200);
    }



}