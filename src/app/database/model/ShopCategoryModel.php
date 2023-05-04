<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\model
 * Class ShopCategoryModel
 * Created By ankio.
 * Date : 2023/5/4
 * Time : 17:35
 * Description :
 */

namespace app\database\model;

use library\database\object\Model;
use library\database\object\SqlKey;

class ShopCategoryModel extends Model
{
    public int $id = 0;
    public string $name = "";

    /**
     * @inheritDoc
     */
    function getPrimaryKey()
    {
        return new SqlKey('id', 0, true);
    }
}