<?php

namespace app\objects\app;

use library\verity\VerityRule;

class PushObject extends BaseObject
{
    public int $type = 0;
    public float $price = 0.00;

    public function getRules(): array
    {
        return array_merge(parent::getRules(), ['type' => new VerityRule('^3|4$', "类型错误", false), 'price' => new VerityRule(VerityRule::FLOAT_AND_INT, '金额错误', false)]);
    }
}