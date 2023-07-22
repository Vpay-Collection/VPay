<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\controller\api
 * Class Sign
 * Created By ankio.
 * Date : 2023/7/22
 * Time : 11:57
 * Description :
 */

namespace app\controller\api;

use library\login\SignUtils;

class Sign extends BaseController
{
    function create(): string
    {
        if(post("key") !== null){
            $data = post();
            unset($data["key"]);
            return $this->json(200,null,SignUtils::sign($data,post("key")));
        }

        return $this->json(403,"缺少加密密钥");
    }
}