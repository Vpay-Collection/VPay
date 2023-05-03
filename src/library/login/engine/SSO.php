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
                $this->logout();
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
        if (empty($token) || $device !== $this->getDevice()) {
            $this->logout();
            return false;
        }
        if (time() - $last < 120) {
            return true;
        }
        Session::getInstance()->set('check', time());
        $data = $this->request('api/login/islogin', ['token' => $token]);
        return $data['code'] === 200;
    }

    //检查是否登录，1分钟检查一次，频率过高会导致cc攻击

    private function request($url, $data = [])
    {
        return AnkioApi::getInstance()->request($url, $data);
    }

    function logout(): void
    {
        $this->request('api/login/logout', ['token' => Session::getInstance()->get("token")]);
        Session::getInstance()->destroy();
    }

    private function callback(CallbackObject $object)
    {
        $result = $this->request('api/login/replace', ['code' => $object->code]);
        Log::record('SSO', Json::encode($result));
        if (isset($result['code']) && $result['code'] === 200) {
            Session::getInstance()->set('token', $result['data']['token']);
            Session::getInstance()->set('device', $this->getDevice());
            EventManager::trigger("__login_success__", $result['data']['user']);
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
        $url = SignUtils::sign([
            'ts' => time(),
            'id' => AnkioApi::getInstance()->appId,
            'host' => Response::getHttpScheme() . Request::getDomain(),
            'redirect' => Request::getNowAddress(),
            't' => 'fingerprint'
        ], AnkioApi::getInstance()->secretKey);
        return AnkioApi::getInstance()->url . '?' . http_build_query($url);
    }
}