<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\dao
 * Class ShopItemDao
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 15:34
 * Description :
 */

namespace app\database\dao;

use app\database\model\ShopItemModel;
use library\database\object\Dao;

class ShopItemDao extends Dao
{

    public function __construct()
    {
        parent::__construct(ShopItemModel::class);
    }

    /**
     * @inheritDoc
     */
    protected function getTable(): string
    {
        return 'shop_item';
    }

    public function getById($id): ?ShopItemModel
    {
        return $this->find(null, ['id' => $id]);
    }

    function delById($id)
    {
        $this->delete()->where(['id' => $id])->commit();
    }

    function getAllItems()
    {
        return $this->select('id', 'item_name', 'icon', 'item_price', 'item_category')->commit(false);
    }


}