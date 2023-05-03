<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\api
 * Class Official
 * Created By ankio.
 * Date : 2023/3/20
 * Time : 12:38
 * Description :
 */

namespace app\controller\api;

use app\utils\official\alipay\AlipayOfficial;

class Official extends BaseController
{
    /**
     * 阿里回调
     **/
    function notify(): string
    {
        return AlipayOfficial::onNotify();
    }
}