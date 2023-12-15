<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace app\database\model;

use library\database\object\Model;

class ShopModel extends Model
{
    public string $item = ""; //商品名称
    public string $image  = "";//头图
    public string $description = "";//商品描述信息
    public float $price = 0.00;//商品价格
    public string $inputs = "";//允许用户输入的字段名
    public string $api = "";//webhook API
    public string $category = "默认"; //
    public bool $stop = false;//是否停售
    public function getNofilter(): array
    {
        return ['description'];//描述是富文本
    }
}