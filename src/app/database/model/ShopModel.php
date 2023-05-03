<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\model
 * Class ShopModel
 * Created By ankio.
 * Date : 2023/3/9
 * Time : 13:01
 * Description :
 */

namespace app\database\model;

use library\database\object\Model;
use library\database\object\SqlKey;

class ShopModel extends Model
{

    public int $id = 0;


    /**
     * @inheritDoc
     */
    function getPrimaryKey(): SqlKey
    {
        return new SqlKey('id', 0, true);
    }
}