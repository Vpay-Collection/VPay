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
use app\core\config\Config;
use app\core\core\Clean;
use app\core\web\Response;
use PDO;
use PDOException;

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
                    ['name' => 'stream_context_create'],
                    ['name' => 'stream_socket_client'],
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
    function set(){
        $arg = $_POST;
        foreach ($arg as $key=>$value){
            if($value=="")
                return $this->ret(403,"不允许有字段为空");
        }


        try{
           $dbManager = new PDO(
            "mysql:dbname={$arg["db_db"]};host={$arg["db_host"]};port={$arg["db_port"]}",
            $arg['db_username'],
            $arg['db_password'],
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\'',
                PDO::ATTR_PERSISTENT => true,
            ]);
            $dbManager->exec(file_get_contents(APP_STORAGE.'sql'.DS.'base.sql'));
            copy(APP_CONF."db.example.yml",APP_CONF."db.yml");
            $db =[
                "master"=>[
                    "type"=>"mysql",
                    "host" =>$arg["db_host"],
                    "username"=>$arg['db_username'],
                    "password"=>$arg['db_password'],
                    "port"=>$arg["db_port"],
                    "db"=>$arg["db_db"] ,
                    "charset"=>"utf8"
                ]
            ];
            Config::getInstance("db")->setAll($db);
        } catch (PDOException $e) {
            return $this->ret(403,"数据库连接失败".$e->getMessage());
        }

        if(!Clean::isAvailableClassname($arg["bg_admin"]))
            return $this->ret(403,"后台路径只允许使用字母");
        if($arg["bg_admin"]=="card"||$arg["bg_admin"]=="pay"||$arg["bg_admin"]=="install"){
            return $this->ret(403,"不允许使用{$arg["bg_admin"]}作为后台路径！");
        }
        Config::getInstance("frame")->set("admin",$arg["bg_admin"]);
        if($arg["bg_admin"]!=="administrator")
        rename(APP_PUBLIC.'ui'.DS.'administrator',APP_PUBLIC.'ui'.DS.$arg["bg_admin"]);

        copy(APP_CONF."pay.example.yml",APP_CONF."pay.yml");
        $data =  Config::getInstance("pay")->get();
        $data["user"]["password"]=md5($arg['bg_username'].$arg['bg_password']);
        $data["user"]["username"]=$arg['bg_username'];
        Config::getInstance("pay")->setAll($data);
        return $this->ret(200);
        // $hash1=md5($data["username"].$passwd);
    }

    function success(){
        file_put_contents(APP_CACHE."install.lock","pay.ankio.net");
        return $this->ret(200,null,["admin"=>Config::getInstance("frame")->getOne("admin")]);
    }

    function checkOut(){
        return $this->ret(200);
    }
}