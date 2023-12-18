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

class AppConfig extends VerityObject
{


    public int $validity_minute = 1;

    public string $app_key  = "";

    public string $app_conflict  = "1";

    public string $app_alipay  = "";

    public string $app_wechat  = "";


    /**
     * @inheritDoc
     */
    function getRules(): array
    {
        return [

        ];
    }

}