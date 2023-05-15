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



use app\utils\ImageUpload;
use cleanphp\base\Config;
use cleanphp\base\Request;
use cleanphp\base\Variables;
use library\qrcode\Code;

class Channel extends BaseController
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
        $this->config['timeout'] = post('timeout', 5);
        $this->config['conflict'] = post('conflict', 1);
        Config::setConfig('app', $this->config);
        return $this->json(200, "更新成功");
    }

    function upload(): string
    {
        $image = new ImageUpload("channel");
        $filename = "";
        if ($image->upload($filename)) {
            $result = Code::decode(Variables::getStoragePath("uploads", 'temp', $filename));
            $image->delImage($filename);
            if (empty($result)) {
                return $this->render(400, "请上传二维码文件");
            }
            return $this->render(200, null, $result);
        }
        return $this->render(403, $filename);
    }

    function set(): string
    {
        $channel = Config::getConfig("channel");
        foreach ($channel as $key => &$value) {
            $arg = arg("image_" . $key);
            if (!empty($arg)) {
                $value = $arg;
            }
        }
        Config::setConfig("channel", $channel);
        return $this->render(200, "保存成功");
    }


}