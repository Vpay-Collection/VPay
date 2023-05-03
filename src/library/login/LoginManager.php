<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: ${NAMESPACE}
 * Class LoginManager
 * Created By ankio.
 * Date : 2023/5/3
 * Time : 18:02
 * Description :
 */

namespace library\login;

use cleanphp\base\Config;
use library\login\engine\BaseEngine;
use library\login\engine\Password;
use library\login\engine\SSO;

class LoginManager
{
    private static ?LoginManager $loginManager = null;
    private ?BaseEngine $engine = null;

    static function init(): LoginManager
    {
        if (self::$loginManager == null) {
            self::$loginManager = new LoginManager();
        }
        return self::$loginManager;
    }

    public function __construct()
    {
        $config = Config::getConfig('sso');
        if (empty($config)) {
            $this->engine = new Password();
        } else {
            $this->engine = new SSO();
        }
    }

    function isLogin(): bool
    {
        return $this->engine->isLogin();
    }

    public function route($a)
    {
        $this->engine->route($a);
    }

    public function logout()
    {
        $this->engine->logout();
    }

    public function getLoginUrl(): string
    {
        return $this->engine->getLoginUrl();
    }
}