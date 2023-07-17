<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\dao
 * Class ShopCategoryDao
 * Created By ankio.
 * Date : 2023/5/4
 * Time : 17:42
 * Description :
 */

namespace app\database\dao;

use app\database\model\ShopCategoryModel;
use library\database\object\Dao;

class ShopCategoryDao extends Dao
{



    function getAllCategory()
    {
        return $this->select()->commit(false);
    }



    function onCreateTable()
    {
        $this->insert()->keyValue(['name' => "未分类"])->commit();
    }

    function delById($id)
    {
        if ($id == 1) return;
        $this->delete()->where(['id' => $id])->commit();
        ShopItemDao::getInstance()->setOption('item_category', $id, 'item_category', 1);
    }
}