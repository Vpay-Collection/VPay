<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\objects\app
 * Class PushObject
 * Created By ankio.
 * Date : 2023/3/29
 * Time : 20:07
 * Description :
 */

namespace app\objects\app;

use library\verity\VerityRule;

class PushObject extends BaseObject
{
    public int $type = 0;
    public float $price = 0.00;

    public function getRules(): array
    {
        return array_merge(parent::getRules(), [
            'type' => new VerityRule('^1|2$', "类型错误", false),
            'price' => new VerityRule(VerityRule::FLOAT_AND_INT, '金额错误', false)
        ]);
    }
}