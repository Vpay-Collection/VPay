<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\admin
 * Class Order
 * Created By ankio.
 * Date : 2023/5/4
 * Time : 17:07
 * Description :
 */

namespace app\controller\admin;

use app\database\dao\AppDao;
use cleanphp\engine\EngineManager;

class Order extends BaseController
{
    function index()
    {
        EngineManager::getEngine()->setData("app", AppDao::getInstance()->getAllApp());
    }
}