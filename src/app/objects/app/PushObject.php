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
            'type' => new VerityRule('^3|4$', "类型错误", false),
            'price' => new VerityRule('^[0-9]+(,[0-9]{3})*(\.[0-9]{2})?$', '金额错误', false)
        ]);
    }
}