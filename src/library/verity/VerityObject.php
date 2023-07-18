<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\verity
 * Class VerityObject
 * Created By ankio.
 * Date : 2022/11/21
 * Time : 00:01
 * Description :
 */

namespace library\verity;

use cleanphp\base\ArgObject;

abstract class VerityObject extends ArgObject
{
    private bool $check;
    public function __construct(?array $item = [],$check = true)
    {
        $this->check = $check;
        parent::__construct($item);
    }

    /**
     * 获取匹配规则
     * @return array
     */
    abstract function getRules(): array;

    use VerityTrait;
}