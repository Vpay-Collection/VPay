<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\api_admin
 * Class Channel
 * Created By ankio.
 * Date : 2023/3/15
 * Time : 12:02
 * Description :
 */

namespace app\controller\api_admin;

use app\utils\GithubUpdater;
use core\base\Request;
use core\config\Config;

class AppChannel extends BaseController
{
    private $config;

    public function __init(): ?string
    {

        $result = parent::__init();
        if ($result !== null) {
            return $result;
        }
        $this->config = Config::getConfig("app");
        return null;
    }

    /**
     * 处理配置信息
     * @return string
     */
    function config(): string
    {
        if (Request::isGet()) return $this->json(200, null, $this->config);
        $this->config['key'] = post('key', '');
        $this->config['conflict'] = post('conflict', 1);
        Config::setConfig('app', $this->config);
        return $this->json();
    }

    /**
     * 检查更新
     * @return string
     */
    function state(): string
    {
        $result = GithubUpdater::init("Vpay-Collection/vpay-android")->check($this->config['ver'], $new, $download);
        return $this->json(200, null, [
            'active' => \app\utils\channel\AppChannel::isActive(),
            'last' => $this->config['last'],
            'update' => $result,
            'version' => $this->config['ver'],
            'new_version' => $new,
            'download' => $download
        ]);

    }

}