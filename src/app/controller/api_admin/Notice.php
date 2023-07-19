<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\api_admin
 * Class Notice
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 13:24
 * Description :
 */

namespace app\controller\api_admin;

use app\objects\config\NoticeConfig;
use app\task\DailyTasker;
use cleanphp\base\Config;
use cleanphp\base\Request;
use library\mail\AnkioMail;
use library\task\TaskerManager;
use library\task\TaskerTime;
use library\verity\VerityException;

class Notice extends BaseController
{
    private NoticeConfig $config;

    public function __init(): ?string
    {

        $result = parent::__init();
        if ($result !== null) {
            return $result;
        }
        $this->config = new NoticeConfig(Config::getConfig("notice"),false);
        return null;
    }

    /**
     * 处理配置信息
     * @return string
     */
    function config(): string
    {
        if (Request::isGet()) return $this->json(200, null, $this->config->toArray());
        try{
            $this->config->merge(post());
        }catch (VerityException $e){
            return $this->json(403,$e->getMessage());
        }
        Config::setConfig('notice', $this->config->toArray());
        //日报需要处理定时任务
        TaskerManager::del("Vpay日报");
        if ($this->config->daily_notice) {
            TaskerManager::add(TaskerTime::day(23, 50), new DailyTasker(), "Vpay日报", -1);
        }

        return $this->json(200, "更新成功");
    }

    function test(): string
    {
        $file = AnkioMail::compileNotify(
            "#4caf50",
            "#fff",
            '',
            'Vpay',
            "Vpay登录测试",
            "<p>亲爱的用户:</p>
                       <p>该邮件是一封测试邮件<b></b></p>
                ");
        ob_start();
        $result = AnkioMail::send($this->config['received'], "Vpay登录测试", $file, "Vpay", true);
        if ($result) {
            echo "测试成功";
        } else {
            echo "测试失败：$result";
        }
        return $this->json(200, null, ob_get_clean());
    }
}