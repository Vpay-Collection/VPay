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
use app\utils\ImageUpload;
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
        $this->config = new ChannelConfig(Config::getConfig("app"),false);
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
            $this->config->mergeArray(post());
        }catch (VerityException $e){
            return $this->json(403,$e->getMessage());
        }
        Config::setConfig('app', $this->config->toArray());
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


}