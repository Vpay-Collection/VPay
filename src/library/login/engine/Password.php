<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\login
 * Class Password
 * Created By ankio.
 * Date : 2022/11/26
 * Time : 15:46
 * Description :
 */

namespace library\login\engine;


use cleanphp\App;
use cleanphp\base\Config;
use cleanphp\base\EventManager;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Session;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;
use cleanphp\file\File;
use cleanphp\file\Log;
use library\captcha\Captcha;
use library\encryption\EncryptionException;
use library\encryption\RSAEncrypt;

class Password extends BaseEngine
{

    function route($action): void
    {

        $result = EngineManager::getEngine()->render(401, '未登录');
        switch (strtolower($action)) {
            case 'islogin':
            {
                if (!$this->isLogin()) {
                    $result = EngineManager::getEngine()->render(401, '未登录');
                } else {
                    $result = EngineManager::getEngine()->render(200, '已登录');
                }
                break;
            }
            case 'login':
            {
                if (!$this->login()) {
                    $result = EngineManager::getEngine()->render(401, '登录失败');
                } else {
                    $result = EngineManager::getEngine()->render(200, '登录成功');
                }
                break;
            }
            case 'change':
            {
                if (!$this->isLogin()) {
                    $result = EngineManager::getEngine()->render(401, '未登录');
                } elseif (!$this->change()) {
                    $result = EngineManager::getEngine()->render(401, '修改失败');
                } else {
                    $result = EngineManager::getEngine()->render(200, '修改成功');
                }
                break;
            }
            case 'logout':
            {
                $this->logout();
                $result = EngineManager::getEngine()->render(200, '成功退出');
                break;
            }
            case 'key':
            {
                $result = EngineManager::getEngine()->render(200, '获取成功', $this->publicKey());
                break;
            }
            case 'captcha':
            {
                (new Captcha())->create('login');
                break;
            }

        }
        (new Response())->render($result, 200, EngineManager::getEngine()->getContentType())->send();
    }

    function isLogin(): bool
    {

        $token = Session::getInstance()->get('token');
        $device = Session::getInstance()->get('device');

        $token2 = Cache::init()->get('token');
        $device2 = Cache::init()->get('device');

        // dumps($token,$token2,$device,$device2, $this->getDevice());

        if ($token !== $token2 || $device !== $device2 || $device !== $this->getDevice()) {
            $this->logout();
            return false;
        }

        return true;
    }

    function setLogin()
    {
        $data = Config::getConfig('login');
        $hash = md5($data["username"] . $data["password"]);
        $timeout = time() + 3600 * 24;
        $token = sha1($hash . md5($timeout));

        $device = $this->getDevice();

        Session::getInstance()->set('token', $token);
        Session::getInstance()->set('device', $device);
        Cache::init()->set('token', $token);
        Cache::init()->set('device', $device);

        EventManager::trigger("__login_success__");
    }

    function logout(): void
    {
        Session::getInstance()->destroy();
        Cache::init()->del('token');
    }

    function login(): bool
    {
        if (!(new Captcha())->verify('login', arg("code"))) {
            App::$debug && Log::record('Password',"验证码验证失败", Log::TYPE_ERROR);
            return false;
        }
        $public = Variables::getStoragePath("key", "public.key");
        $private = Variables::getStoragePath("key", "private.key");
        $rsa = new RSAEncrypt();
        try {
            $rsa->initRSAPath($private, $public);
        } catch (EncryptionException $e) {
            App::$debug && Log::record('Encrypt', $e->getMessage(), Log::TYPE_ERROR);
            return false;
        }
        $passwd = $rsa->rsaPrivateDecrypt(arg("password"));
        App::$debug && Log::record('Password',"解密密码：$passwd", Log::TYPE_WARNING);
     //   dumps($passwd,arg("password"));
        $user = arg("username");
        $data = Config::getConfig('login');
        if (md5($data["username"] . $passwd) === $data["password"] && $user === $data["username"]) {
            $this->setLogin();
            return true;
        }
        return false;
    }

    function change(): bool
    {
        $username = arg("username");
        $old = arg("old");
        $new = arg("new");
        if (empty($old) || empty($new)) return true;
        $data = Config::getConfig('login');
        if (md5($data["username"] . $old) === $data["password"]) {
            Cache::init()->del("token");
            Session::getInstance()->destroy();
            $data["password"] = md5($username . $new);
            $data["username"] = $username;
            Config::setConfig("login", $data);
            return true;
        }
        return false;
    }

    /**
     * 获取加密公钥
     * @return string
     */
    function publicKey(): string
    {
        $public = Variables::getStoragePath("key", "public.key");
        $private = Variables::getStoragePath("key", "private.key");
        if (is_file($public) && is_file($private)) {
            return file_get_contents($public);
        } else {
            $rsa = new RSAEncrypt();
            $rsa->create();
            $keys = $rsa->getKey();
            File::mkDir(Variables::getStoragePath("key"));
            file_put_contents($public, $keys["public_key"]);
            file_put_contents($private, $keys["private_key"]);
            return $keys["public_key"];
        }
    }

    function getLoginUrl(): string
    {
        $url = Request::getNowAddress();
        return url('index', 'main', 'login', ['redirect' => $url]);
    }

    function getUser(): array
    {
        return [
            'id' => 0,
            'username' => Config::getConfig('login')["username"],
            'image' => Config::getConfig('login')["image"]
        ];
    }
}