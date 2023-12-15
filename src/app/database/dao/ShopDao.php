<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace app\database\dao;

use app\database\model\ShopModel;
use library\database\object\Dao;

class ShopDao extends Dao
{

    public function getById($id): ?ShopModel
    {
        return $this->find(null, ['id' => $id]);
    }

    function delById($id): void
    {
        $this->delete()->where(['id' => $id])->commit();
    }

    function getAllItems()
    {
        return $this->select('id', 'item', 'image', 'price', 'category')->where(['stop'=>false])->commit(false);
    }
}