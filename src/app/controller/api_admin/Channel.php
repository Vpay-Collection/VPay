<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\api_admin
 * Class Channel
 * Created By ankio.
 * Date : 2023/3/15
 * Time : 12:02
 * Description :
 */

namespace app\controller\api_admin;



use app\objects\config\ChannelConfig;
use cleanphp\base\Config;
use cleanphp\base\Request;
use cleanphp\base\Variables;
use library\qrcode\Code;
use library\verity\VerityException;

class Channel extends BaseController
{
    private ChannelConfig $config;

    public function __init(): ?string
    {

        $result = parent::__init();
        if ($result !== null) {
            return $result;
        }
        $this->config = new ChannelConfig(Config::getConfig("alipay"),false);
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
            $this->config= new ChannelConfig(post());
        }catch (VerityException $e){
            return $this->json(403,$e->getMessage());
        }
        Config::setConfig('alipay', $this->config->toArray());
        return $this->json(200, "更新成功");
    }
}