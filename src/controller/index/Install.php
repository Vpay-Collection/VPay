<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Install
 * Created By ankio.
 * Date : 2022/2/3
 * Time : 4:39 下午
 * Description :
 */

namespace app\controller\index;

use app\attach\Check;
use app\core\web\Response;

class Install extends BaseController
{
    public function init()
    {
       $install = file_exists(APP_CACHE."install.lock");
       if($install&&arg("a")!=="start"){
           return $this->ret(403);
       }
       return null;
    }

    function isInstall(): array
    {
        return $this->ret(!file_exists(APP_CACHE."install.lock"));
    }
    function start(){
        Response::location("/ui/install");
    }
    function check(): array
    {
        $check = [
            'env'=>[
                'os'=>['min'=>'不限','good'=>'Linux'],//运行的程序的系统
                'php'=>['min'=>'7.4','good'=>'7.4'],//php支持版本
                'upload'=>['min'=>'2M','good'=>'2M'],//上传附件大小
                'disk'=>['min'=>'12M','good'=>'12M'],//磁盘大小
            ],
            'var'=>[
                'dirfile'=>[
                    ['type' => 'dir', 'path' => '/public'],
                    ['type' => 'dir', 'path' => '/storage'],
                //    ['type' => 'file', 'path' => '/i/img/logo.png'],
                ],//检查某个目录或者文件是否可写
                'func'=>[
                    ['name' => 'json_decode'],
                    ['name' => 'json_encode'],
                    ['name' => 'urldecode'],
                    ['name' => 'urlencode'],
                    ['name' => 'openssl_encrypt'],
                    ['name' => 'openssl_decrypt'],
                    ['name' => 'mb_convert_encoding'],
                    ['name' => 'curl_init'],
                    ['name' => 'curl_init'],
                ],//检查某个函数是否可用
                'ext'=>[
                    ['name' => 'curl'],
                    ['name' => 'openssl'],
                    ['name' => 'gd'],
                    ['name' => 'json'],
                    ['name' => 'session'],
                    ['name' => 'PDO'],
                    ['name' => 'iconv'],
                    ['name' => 'mbstring'],
                    ['name' => 'sockets']
                ]//检查是否加载了对应的php拓展
            ]
        ];
        $checkObj = new Check($check["var"],$check["env"]);
        $data = [
           "func"=> $checkObj->func(),
            "env"=>$checkObj->env(),
            "dirfile"=>$checkObj->dirfile(),
            "ext"=>$checkObj->ext()
        ];
        return $this->ret(200,null,$data);
    }
}