<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\model
 * Class AppModel
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 11:22
 * Description :
 */

namespace app\database\model;

use library\database\object\Model;
use library\database\object\SqlKey;

class AppModel extends Model
{

    public int $id = 0;
    public string $app_name = "";//支付站点的名称
    public string $app_key = "";//支付站点的密钥
    public string $app_image = "";//支付站点的logo

    function getPrimaryKey(): SqlKey
    {
        return new SqlKey('id', 0, true);
    }
}