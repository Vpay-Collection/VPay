<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\api_admin
 * Class Main
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 21:33
 * Description :
 */

namespace app\controller\api_admin;

use app\database\dao\FileDao;
use library\login\LoginManager;

class User extends BaseController
{
    function info()
    {
        return   $this->render(200,null,LoginManager::init()->getUser());
    }
    function upload(): string
    {
        $user = LoginManager::init()->getUser();
        [$error, $name, $url] = FileDao::getInstance()->upload();
        if ($error)
            return $this->render(403, $error);
        FileDao::getInstance()->use($name,$user['image']);
        return $this->render(200, null, $url);
    }

}