<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\api_admin
 * Class Notice
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 13:24
 * Description :
 */

namespace app\controller\api_admin;

use app\task\DailyTasker;
use cleanphp\base\Config;
use cleanphp\base\Request;
use library\mail\AnkioMail;
use library\task\TaskerManager;
use library\task\TaskerTime;

class Notice extends BaseController
{
    private $config;

    public function __init(): ?string
    {

        $result = parent::__init();
        if ($result !== null) {
            return $result;
        }
        $this->config = Config::getConfig("mail");
        return null;
    }

    /**
     * 处理配置信息
     * @return string
     */
    function config(): string
    {
        if (Request::isGet()) return $this->json(200, null, $this->config);
        foreach ($this->config as $key => &$value) {
            $value = post($key, $value);
        }
        Config::setConfig('mail', $this->config);
        //日报需要处理定时任务
        TaskerManager::del("Vpay日报");
        if ($this->config['pay_daily']) {
            TaskerManager::add(TaskerTime::day(23, 50), new DailyTasker(), "Vpay日报", -1, true);
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