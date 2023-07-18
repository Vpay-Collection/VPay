<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

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

class AppModel extends Model
{

    public int $id = 0;
    public string $app_name = "";//支付站点的名称
    public string $app_key = "";//支付站点的密钥
    public string $app_image = "";//支付站点的logo

    public function getDisableKeys(): array
    {
        return ["app_key"];

    }
}