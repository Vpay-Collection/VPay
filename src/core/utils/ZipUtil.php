<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\utils;


use ZipArchive;

/**
 * Class ZipUtil
 * Created By ankio.
 * Date : 2022/1/12
 * Time : 8:12 下午
 * Description :zip工具类
 */
class ZipUtil
{


    function Zip($dir, $zipfilename)
    {
        $zip=new ZipArchive();
        if($zip->open($zipfilename, ZipArchive::CREATE|ZipArchive::OVERWRITE)=== TRUE){
           $this-> addFileToZip($dir, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
            $zip->close(); //关闭处理的zip文件
        }
    }

    /**
     *
     * @param $path
     * @param $zip ZipArchive
     * @return void
     */
    function addFileToZip($path,$zip){
        $handler=opendir($path); //打开当前文件夹由$path指定。
        while(($filename=readdir($handler))!==false){
            if($filename != "." && $filename != ".." && $filename!=".DS_Store"){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
                if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                    $this->addFileToZip($path."/".$filename, $zip);
                }else{ //将文件加入zip对象
                    $zip->addFile($path."/".$filename);
                }
            }
        }
        @closedir($handler);
    }
}

