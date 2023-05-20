<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * Package: app\controller\index
 * Class Install
 * Created By ankio.
 * Date : 2023/5/19
 * Time : 12:20
 * Description :
 */

namespace app\controller\index;

use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;

class Install extends Controller
{
    function index(){
        if(!empty(Cache::init()->get("install"))){
            Response::location(url('index','main','index'));
        }
        $envs = [
            [
                'name' => '操作系统',
                'pref' => 'Linux',
                'current' => PHP_OS,
                'status' =>  true
            ],
            [
                'name' => 'PHP版本',
                'pref' => '7.4',
                'current' => PHP_VERSION,
                'status' =>  version_compare(PHP_VERSION, '7.4.0', '>=')
            ],
            [
                'name' => '附件上传',
                'pref' => '50M',
                'current' => ini_get('upload_max_filesize'),
                'status' =>  intval(ini_get('upload_max_filesize')) >= 50
            ],
        ];

        $dirs = [
            [
                'name' => Variables::getStoragePath(),
                'status' =>  is_writable(Variables::getStoragePath()) && is_readable(Variables::getStoragePath())
            ]
        ];

        $functions = $this->checkFunctionAvailability([
            'file_exists',
            'is_file',
            'is_dir',
            'fopen',
            'fclose',
            'fwrite',
            'fread',
            'file_get_contents',
            'file_put_contents',
            'copy',
            'rename',
            'unlink',
            'mkdir',
            'rmdir',
            'scandir',
            'glob',
            'serialize',
            'unserialize',
            'json_encode',
            'json_decode',
            'curl_init',
            'curl_exec',
            'curl_getinfo',
            'curl_setopt',
            'fsockopen',
            'stream_socket_client',
            'gethostbyname',
            'gethostbynamel',
            'parse_url',
            'urlencode',
            'urldecode',
            'http_build_query',
            'header',
            'setcookie',
            'get_headers',
            'stream_context_create',
        ]);

        $extends = $this->checkExtensionAvailability([
            'gd',
            'curl',
            'mbstring',
            'openssl',
            'sockets',
            'pdo',
            'pdo_mysql',
        ]);





        EngineManager::getEngine()->setLayout("layout")
            ->setData("title","Vpay安装 V".Config::getConfig('frame')['version'])
            ->setData("image","/clean_static/img/head.jpg")
        ->setData("envs",$envs)
        ->setData("dirs",$dirs)
        ->setData("functions",$functions)
        ->setData("extends",$extends)
        ->setData("domain",Request::getDomain());


    }

    private function checkFunctionAvailability($functions): array
    {
        $result = array();

        foreach ($functions as $function) {
            $status = function_exists($function);

            $result[] = array(
                'name' => $function,
                'status' => $status
            );
        }

        return $result;
    }

   private function checkExtensionAvailability($extensions): array
   {
        $result = array();

        foreach ($extensions as $extension) {
            $status = extension_loaded($extension);

            $result[] = array(
                'name' => $extension,
                'status' => $status
            );
        }

        return $result;
    }

}