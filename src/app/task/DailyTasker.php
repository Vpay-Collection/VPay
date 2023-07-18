<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\task
 * Class MailTasker
 * Created By ankio.
 * Date : 2023/5/15
 * Time : 15:28
 * Description :
 */

namespace app\task;

use app\database\dao\OrderDao;
use cleanphp\base\Config;
use library\mail\AnkioMail;
use library\task\TaskerAbstract;
use Throwable;

class DailyTasker extends TaskerAbstract
{

    /**
     * @inheritDoc
     */
    public function getTimeOut(): int
    {
        return 600;
    }

    /**
     * @inheritDoc
     */
    public function onStart()
    {

        $file = AnkioMail::compileNotify("#FF5722", "#fff", Config::getConfig("login")['image'], "Vpay", "日报 - " . date("Y-m-d"), "<p>今日总收入：￥<span>" . OrderDao::getInstance()->getToday() . "</span></p><p>累计收入：￥<span>" . OrderDao::getInstance()->getTotal() . "</span></p>");

        AnkioMail::send(Config::getConfig("mail")['received'], "日报 - " . date("Y-m-d"), $file, "Vpay");

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