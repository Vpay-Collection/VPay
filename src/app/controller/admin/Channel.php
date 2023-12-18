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

namespace app\controller\admin;



use app\database\dao\FileDao;
use app\database\model\FileModel;
use app\objects\config\AlipayConfig;
use app\objects\config\AppConfig;
use cleanphp\base\Config;
use cleanphp\base\Request;
use library\qrcode\Code;
use library\upload\UploadFile;
use library\verity\VerityException;

class Channel extends BaseController
{

    /**
     * 处理配置信息
     * @return string
     */
    function config(): string
    {
        $config = new AlipayConfig(Config::getConfig("alipay"),false);
        if (Request::isGet()) return $this->json(200, null, $config->toArray());
        try{
            $config= new AlipayConfig(post());
        }catch (VerityException $e){
            return $this->json(403,$e->getMessage());
        }
        Config::setConfig('alipay', $config->toArray());
        return $this->json(200, "更新成功");
    }

    /**
     * 处理配置信息
     * @return string
     */
    function app(): string
    {
        $config = new AppConfig(Config::getConfig("app"),false);
        if(empty($config->app_key)){
            $config->app_key = rand_str(16);
        }
        if (Request::isGet()) return $this->json(200, null, $config->toArray());
        try{
            $config= new AppConfig(post());
        }catch (VerityException $e){
            return $this->json(403,$e->getMessage());
        }
        Config::setConfig('app', $config->toArray());
        return $this->json(200, "更新成功");
    }
    function upload(): string
    {
        /**
         * @var $file UploadFile
         */
        [$error, $file, $url] = FileDao::getInstance()->upload();
       $path =  $file->path.DS. basename($url);
       $decode = Code::decode($path);
       $url = url('api','image','qrcode',['url'=>$decode,'type'=>'.jpg']);
       return $this->render(200,null,$url);

    }
}