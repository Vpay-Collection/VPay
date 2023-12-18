<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\objects\config
 * Class ChannelConfig
 * Created By ankio.
 * Date : 2023/7/18
 * Time : 13:23
 * Description :
 */

namespace app\objects\config;

use library\verity\VerityObject;
use library\verity\VerityRule;

class AlipayConfig extends VerityObject
{

    public string $alipay_id = "";
    public string $alipay_private_key = "";
    public int $validity_minute = 1;
    public string $alipay_public_key  = "";




    /**
     * @inheritDoc
     */
    function getRules(): array
    {
        return [
            "alipay_id"=>new VerityRule("^\w{16}$","当面付ID为16位",false),
            "validity_minute"=>new VerityRule("^\d+$","时间必须为整数",false),
            "alipay_private_key|alipay_public_key"=>new VerityRule("","密钥不允许为空",false)
        ];
    }

}