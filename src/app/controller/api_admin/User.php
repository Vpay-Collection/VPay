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

use app\utils\ImageUpload;
use cleanphp\base\Config;

class User extends BaseController
{
    function upload(): string
    {
        $image = new ImageUpload("user");
        $filename = "";
        if ($image->upload($filename)) {
            $login = Config::getConfig("login");
            $image->delImage($login["image"]);
            $login["image"] = $image->useImage($filename);
            Config::setConfig("login", $login);
            return $this->render(200, null, $filename);
        }
        return $this->render(403, $filename);
    }
}