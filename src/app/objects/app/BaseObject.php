<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

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
        if (!isset($item['sign'])) throw new VerityException('缺少签名');
        unset($item['sign']);
        try {
            $sign = md5(Json::encode($item) . $key);
        } catch (\JsonException $e) {
            throw new VerityException('签名校验错误：' . $e->getMessage());
        }
        if ($sign !== $this->sign) throw new VerityException("签名校验失败：".Json::encode($item));

        if (time() + 300 < $this->t) throw new VerityException('时间过期了');
    }


    function getRules(): array
    {
        return [
            't' => new VerityRule('^\d{10}$', '时间戳格式错误', false),
            'sign' => new VerityRule('^\w{32}$', '签名错误', false)
        ];
    }
}