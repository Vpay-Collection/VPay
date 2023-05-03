<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\login\objects
 * Class CallbackObject
 * Created By ankio.
 * Date : 2022/11/27
 * Time : 14:46
 * Description :
 */

namespace library\login;

use library\user\login\Sign;
use library\verity\VerityException;
use library\verity\VerityObject;

class CallbackObject extends VerityObject
{

    public string $sign = "";//签名
    public int $t = 0;//时间戳
    public string $code = "";//refresh_code
    public string $redirect = "";

    /**
     * @throws VerityException
     */
    public function __construct(array $item = [], string $key = '')
    {
        parent::__construct($item);
        $item = $this->toArray();
        if (!Sign::checkSign($item, $key)) {
            throw new VerityException('签名验证失败');
        }
        if (time() - $this->t > 300) {
            throw new VerityException('链接超时');
        }
        $this->redirect = urldecode($this->redirect);
    }

    /**
     * @inheritDoc
     */
    function getRules(): array
    {
        return [
            't' => '^\d{10}$',
            'sign' => '^\w{64}$',
        ];
    }
}