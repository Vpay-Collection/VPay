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

use core\base\Request;
use core\config\Config;

class OfficialChannel extends BaseController
{
    private $config;

    public function __init(): ?string
    {

        $result = parent::__init();
        if ($result !== null) {
            return $result;
        }
        $this->config = Config::getConfig("official");
        return null;
    }

    /**
     * 处理配置信息
     * @return string
     */
    function config(): string
    {
        if (Request::isGet()) return $this->json(200, null, $this->config);
        $this->config['id'] = post('id', '');
        $this->config['private'] = post('private', '');
        $this->config['public'] = post('public', '');
        Config::setConfig('official', $this->config);
        return $this->json();
    }


}