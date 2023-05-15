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
use cleanphp\base\Response;
use cleanphp\engine\EngineManager;
use library\login\LoginManager;

class Main extends BaseController
{

    function index()
    {
       [$day, $data] = OrderDao::getInstance()->countData();
        EngineManager::getEngine()
            ->setData("total_price", OrderDao::getInstance()->getTotal())
            ->setData("today_price", OrderDao::getInstance()->getToday())
            ->setData("payments", OrderDao::getInstance()->getRecently(10))
            ->setData("day", array_reverse($day))
            ->setData("data", array_reverse($data));
    }

    function logout()
    {
        LoginManager::init()->logout();
        Response::location(url("admin", "main", "index"));
    }
}