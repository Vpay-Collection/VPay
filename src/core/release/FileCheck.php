<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\release;


use app\core\debug\Log;
use app\core\utils\StringUtil;

class FileCheck
{
    private static $info = "";
    private static $errCount = 0;
    private static $warnCount = 0;
    private static $no_check=[];

    /**
     * 开始进行安全性检查
     * @return void
     */
    public static function run()
    {
        self::$errCount = 0;
        self::$warnCount = 0;
        self::$info = "[信息]安全性检查开始";
        self::checkPHPInUI();
        self::checkController();
        self::checkShell();
        self::$info .= "\n[信息]安全性检查结束，总计" . self::$warnCount . "个警告，" . self::$errCount . "个错误。";
        echo self::$info;
    }

    public static function setNoCheck($arr){

        self::$no_check=array_merge(self::$no_check,$arr);
    }
    public static function checkController(){
        self::$info .= "\n[信息]正在执行不规范写法检查";
        $files = self::getAllfile(APP_CONTROLLER);
        $files2 = self::getAllfile(APP_MODEL);
        $files=array_merge($files,$files2);
        self::doFile($files,function ($f){
            // $content=strtolower(file_get_contents($f));
            preg_match_all("/(echo|var_dump|die|exit|print|printf)(\(|\s)/",strtolower(file_get_contents($f)),$matches);
            if(sizeof($matches)!=0){
                if(sizeof($matches[0])!=0){
                    self::$errCount++;
                    self::$info .= "\n[错误]文件 $f 存在不规范的函数使用(".str_replace("\n","",str_replace("(","",$matches[0][0])).")！处理建议：输出内容请直接return，调试输出请使用内置函数，退出程序运行请使用exitApp()函数。";
                    //  dump(self::$info);
                }

            }
        });
    }
    public static function checkPHPInUI()
    {
        self::$info .= "\n[信息]正在执行公开目录可执行文件检查";
        $files = self::getAllfile(APP_PUBLIC);
        //检查公开目录的是否存在php
        self::doFile($files, function ($file) {
            if (!StringUtil::get($file)->endsWith("index.php") && StringUtil::get($file)->endsWith(".php")) {
                self::$warnCount++;
                self::$info .= "\n[警告] " . $file . " 存在可执行PHP文件。 修改建议：public目录下除了index.php不要放任何php文件。";
            }
        });
        $files = self::getAllfile(APP_STORAGE);
        // 检查缓存路径是否存在在php
        self::doFile($files, function ($file) {
            if (StringUtil::get($file)->endsWith(".php")) {
                self::$warnCount++;
                self::$info .= "\n[警告] " . $file . " 存在可执行PHP文件。 修改建议：storage路径为临时缓存与存储路径，请不要在此处放任何可执行php文件,因为在APP打包时会进行清理。";
            }
        });
        $files = self::getAllfile(APP_CONF);
        // 检查配置路径是否存在不标准文件
        self::doFile($files, function ($file) {
            if (!StringUtil::get($file)->endsWith(".DS_Store") && !StringUtil::get($file)->endsWith(".yml")) {
                self::$warnCount++;
                self::$info .= "\n[警告] " . $file . " 存在非配置文件。 修改建议：配置路径请勿放其他无关文件，建议删除该文件。";
            }
        });

    }

    private static function doFile($fileList, $fnName)
    {
        if (is_array($fileList) && sizeof($fileList) != 0) {
            foreach ($fileList as $key => $file) {
                self::doFile($file, $fnName);
            }
        }
        if (!is_array($fileList) && is_file($fileList)) {
            call_user_func_array($fnName, [$fileList]);
        }

    }

    public static function checkShell()
    {
        self::$info .= "\n[信息]正在检查可能存在的恶意代码";
        $functions = [
            '/(\s|\(|=)(system|passthru|shell_exec|exec|popen|proc_open)\(/'=>"可能导致系统命令执行，属于高危函数，请谨慎使用。",
            '/(\s|\(|=)(eval|assert|call_user_func|gzinflate|gzuncompress|gzdecode|str_rot13)\(/'=>"可能导致任意代码执行，请谨慎使用。",
            '/(\s|\(|=)(require|require_once|include|include_once)\(/'=>"可能导致任意文件包含，代码中请直接规范使用命名空间来避免包含文件。",
            '/\$_(GET|POST|REQUEST|COOKIE|SERVER|FILES)/'=>"可能导致不可控的用户输入，请使用内置的arg函数获取用户数据。",
            '/(\$\w+)\(/'=>"可能导致不可控的函数执行，请尽量明确执行函数。",
            ];

        self::$no_check[]="/core";
        self::$no_check[]="/vendor";
        self::$no_check[]="/public/index.php";
        $file=self::getAllfile(APP_DIR);

        unset(self::$no_check[sizeof(self::$no_check)-1]);
        unset(self::$no_check[sizeof(self::$no_check)-1]);
        self::doFile($file,function ($f) use ($functions) {
            if(!StringUtil::get($f)->endsWith(".php"))return;
            foreach ($functions as $key=>$value){
                preg_match_all($key,strtolower(file_get_contents($f)),$matches);
              //  dump($matches);
                if(sizeof($matches)!=0){
                    if(sizeof($matches[0])!=0){
                        self::$warnCount++;
                        self::$info .= "\n[警告]文件 $f 存在可疑的(".str_replace("\n","",str_replace("(","",$matches[0][0])).")调用！处理建议：$value";
                      //  dump(self::$info);
                    }

                }
        }
        });



    }

    public static function checkMd5($dir,$md5)
    {
        return $md5==self::getMd5($dir,$dir);
    }




    public static  function getMd5($raw,$dir)
    {

        $no_check=[
            "/storage",
            ".storage",
            ".DS_Store",
            "/config",
            ".db",
            ".lock"
        ];
        if (!is_dir($dir)) {
            return "";
        }

        $filemd5s = [];
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {

            if ($entry != '.' && $entry != '..' && $entry != '.svn') {
                $file=str_replace("//","/",str_replace($raw,"",$dir). '/' .$entry);

                $find=false;
                foreach ($no_check as $v){

                    if(StringUtil::get($file)->contains($v)){
                        $find=true;
                        break;
                    }
                }
                if($find)continue;

                if (is_dir($dir . '/' . $entry)) {

                    $filemd5s[] = self::getMd5($raw,$dir . '/' . $entry);

                } else {
                    $md5=md5_file($dir . '/' . $entry);
                   // echo $entry."   ->    ".$md5."\n";
                    $filemd5s[] = $md5;

                }

            }

        }

        $d->close();
       // echo implode('', $filemd5s)."\n";
        return md5(str_replace("d41d8cd98f00b204e9800998ecf8427e","",implode('', $filemd5s)));

    }

    private static function getAllfile($dir)
    {
        $files = array();
        if ($head = opendir($dir)) {
            while (($entry = readdir($head)) !== false) {
                $file=str_replace("//","/",str_replace(APP_DIR,"",$dir). '/' .$entry);
                $find=false;
                foreach (self::$no_check as $v){
                    if(StringUtil::get($file)->startsWith($v)){
                        $find=true;
                        break;
                    }
                }
                if($find)continue;
                if ($entry != ".." && $entry != ".") {
                    if (is_dir($dir . '/' . $entry)) {
                        $files[$entry] = self::getAllfile($dir . '/' . $entry);
                    } else {
                        $files[] = $dir . '/' . $entry;
                    }
                }
            }
        }
        closedir($head);
        return $files;
    }
}