<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\objects\app
 * Class BaseObject
 * Created By ankio.
 * Date : 2023/3/29
 * Time : 20:07
 * Description :
 */

namespace app\objects\app;

use cleanphp\base\Json;
use library\login\SignUtils;
use library\verity\VerityException;
use library\verity\VerityObject;
use library\verity\VerityRule;

class BaseObject extends VerityObject
{
    public int $t = 0;
    public string $sign = "";

    /**
     * @throws VerityException
     */
    public function __construct(array $item = [], $key = "")
    {


        parent::__construct($item);
        if (!SignUtils::checkSign($item, $key)) {
            throw new VerityException('签名校验失败');
        }
        if (time() + 300 < $this->t) throw new VerityException('签名时间过期');
    }


    function getRules(): array
    {
        return [
            't' => new VerityRule('^\d{10}$', '时间戳格式错误', false),
            'sign' => new VerityRule('^\w{32}$', '签名错误', false)
        ];
    }
}