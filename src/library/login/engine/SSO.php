<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\login\engine;

use cleanphp\base\EventManager;
use cleanphp\base\Json;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Session;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;
use cleanphp\file\Log;
use library\login\AnkioApi;
use library\login\CallbackObject;
use library\login\SignUtils;
use library\verity\VerityException;


class SSO extends BaseEngine
{
    function route($action): void
    {
        $result = EngineManager::getEngine()->render(401, '未登录');

        switch (strtolower($action)) {
            case 'islogin':
            {
                if ($this->isLogin()) {
                    $result = EngineManager::getEngine()->render(401, '未登录');
                } else {
                    $result = EngineManager::getEngine()->render(200, '已登录');
                }
                break;
            }
            case 'logout':
            {
                $this->logout($_GET['token']);
                $result = EngineManager::getEngine()->render(200, '成功退出');
                break;
            }
            case 'callback':
            {
                try {
                    $object = new CallbackObject(arg(), AnkioApi::getInstance()->secretKey);
                    $result = $this->callback($object);
                    if ($result === true) {
                        EventManager::trigger("__login_success__");
                        Response::location($object->redirect);
                    }
                } catch (VerityException $e) {
                    $result = EngineManager::getEngine()->render(403, $e->getMessage());
                }
                //进行回调
                break;
            }
        }
        (new Response())->render($result, 200, EngineManager::getEngine()->getContentType())->send();
    }

    function isLogin(): bool
    {
        $last = Session::getInstance()->get('check', 0);
        $token = Session::getInstance()->get('token');
        $device = Session::getInstance()->get('device');

        if (empty($token) || $device !== $this->getDevice() || empty(Cache::init(0,Variables::getCachePath('tokens'))->get($token))) {
            $this->logout();
            return false;
        }
        if (time() - $last < 600) { //10分钟检查一次避免过高的请求
            return true;
        }
        Session::getInstance()->set('check', time());
        $data = $this->request('api/login/islogin', ['token' => $token]);
        if($data['code'] === 200){
            return true;
        }
        $this->logout();
        return false;
    }

    private function request($url, $data = [])
    {
        return AnkioApi::getInstance()->request($url, $data);
    }

    function logout($token = null): void
    {
        if($token!==null){
            Cache::init(0,Variables::getCachePath('tokens'))->del($token);
        }else{
            $token = Session::getInstance()->get("token");
            if(!empty($token)){
                $this->request('api/login/logout', ['token' => $token]);
                Cache::init(0,Variables::getCachePath('tokens'))->del($token);
                Session::getInstance()->destroy();
            }


        }

    }

    private function callback(CallbackObject $object)
    {
        $result = $this->request('api/login/replace', ['code' => $object->code]);
        Log::record('SSO', Json::encode($result));
        if (isset($result['code']) && $result['code'] === 200) {
            Session::getInstance()->set('token', $result['data']['token']);
            Session::getInstance()->set('device', $this->getDevice());
            $result['data']['username'] = $result['data']['nickname'];
            Session::getInstance()->set("user",$result['data']);
            Cache::init(0,Variables::getCachePath('tokens'))->set($result['data']['token'],$result['data']['token']);
            EventManager::trigger("__login_success__", $result['data']);
            return true;
        } else {
            return $result['msg'];
        }
    }

    /**
     * 获取登录地址
     * @return string
     */
    function getLoginUrl(): string
    {
        return AnkioApi::getInstance()->url . '?' . http_build_query([
                'id' => AnkioApi::getInstance()->appId,
                'redirect' => Request::getNowAddress()
            ]);
    }


    function getUser(): array
    {
        return  Session::getInstance()->get("user");
    }
}