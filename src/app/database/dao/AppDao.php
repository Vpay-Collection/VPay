<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\dao
 * Class AppDao
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 11:24
 * Description :
 */

namespace app\database\dao;

use app\database\model\AppModel;
use cleanphp\base\Config;
use cleanphp\base\Request;
use Couchbase\RegexpSearchQuery;
use library\database\object\Dao;

class AppDao extends Dao
{



    public function getByAppId($appid): ?AppModel
    {

        return $this->find(null, ['id' => $appid]);
    }

    public function getAllApp()
    {
        return $this->select()->commit(false);
    }

    public function del($id)
    {
        if ($id === 0) return;
        $this->delete()->where(['id' => $id])->commit();
    }

    public function onCreateTable(): void
    {
        $host = Request::getAddress();
        $key = rand_str(32);
        $shop = Config::getConfig('shop');
        $shop['host'] = $host;
        $shop['key'] = $key;
        Config::setConfig('shop',$shop);

       $this->insert()->keyValue([
           'id'=>1,
           'app_name'=>'内置商城',
           'app_key'=>$key,
           'app_image'=>$host."/clean_static/img/shop.png",
       ])->commit();
    }

}