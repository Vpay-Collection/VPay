<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\database\model
 * Class ShopItemModel
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 15:29
 * Description :
 */

namespace app\database\model;

use library\database\object\Model;
use library\database\object\SqlKey;

class ShopItemModel extends Model
{


    public string $item_name = "";//商品名
    public string $icon = "";
    public float $item_price = 0.01;
    public int $item_category = 1;
    public string $inputs = "";
    public string $webhook = "";//Hook地址

    public string $description = ""; //商品描述

    public function getNofilter(): array
    {
        return ['description'];
    }
}