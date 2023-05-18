<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * Package: app\task
 * Class DaemonTasker
 * Created By ankio.
 * Date : 2023/5/18
 * Time : 13:52
 * Description :
 */

namespace app\task;

use cleanphp\base\Config;
use cleanphp\cache\Cache;
use library\mail\AnkioMail;
use library\task\TaskerAbstract;
use Throwable;

class DaemonTasker extends TaskerAbstract
{

    /**
     * @inheritDoc
     */
    public function getTimeOut(): int
    {
        return 60;
    }

    /**
     * @inheritDoc
     */
    public function onStart()
    {
        $last = Cache::init()->get("last_heart");
        $online = false;
        if (time() - $last <= 60 * 15) {
            $online = true;
        }
        if(!$online){
            $file = AnkioMail::compileNotify("#e74c3c", "#fff",  Config::getConfig("login")['image'],"Vpay", "App客户端心跳掉线", "<p>App客户端心跳掉线，请检查手机端监控</p><p>最后心跳时间：" . date("Y-m-d H:i:s", $last) . "</p>");
            AnkioMail::send(Config::getConfig("mail")['received'], "App客户端心跳掉线", $file, "Vpay");
        }

    }

    /**
     * @inheritDoc
     */
    public function onStop()
    {

    }

    /**
     * @inheritDoc
     */
    public function onAbort(Throwable $e)
    {

    }
}