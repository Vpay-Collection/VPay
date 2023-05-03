<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class Main
 * Created By ankio.
 * Date : 2023/3/14
 * Time : 22:11
 * Description :
 */

namespace app\controller\admin;

use app\database\dao\OrderDao;

class Main extends BaseController
{

    function console()
    {
        $this->eng()->setData("total_price", OrderDao::getInstance()->getTotal());
        $this->eng()->setData("today_price", OrderDao::getInstance()->getToday());
        $this->eng()->setData("payments", OrderDao::getInstance()->getRecently(5));
    }
}