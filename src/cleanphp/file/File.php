<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace cleanphp\file;


use ZipArchive;

/**
 * Class File
 * Created By ankio.
 * Date : 2022/1/12
 * Time : 8:09 下午
 * Description : 文件工具类
 *
 */
class File
{

    /**
     * 文件夹删除或者文件删除
     * @param string $name
     * @return bool
     */
    public static function del(string $name): bool
    {
        if (!is_dir($name)) {
            if (is_file($name))
                return unlink($name);
            else
                return false;
        }
        $handle = opendir($name); //打开目录
        while (($file = readdir($handle)) !== false) {
            if ($file !== "." && $file !== "..") {
                $dir = $name . '/' . $file;
                is_dir($dir) ? self::del($dir) : unlink($dir);
            }
        }
        closedir($handle);
        return rmdir($name);
    }

    /**
     * 清空一个文件夹
     * @param string $path
     */
    static function empty(string $path)
    {
        //如果是目录则继续
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach ($p as $val) {
                //排除目录中的.和..
                if ($val != "." && $val != "..") {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . $val)) {
                        //子目录中操作删除文件夹和文件
                        self::empty($path . $val . '/');
                        //目录清空后删除空文件夹
                        @rmdir($path . $val . '/');
                    } else {
                        //如果是文件直接删除
                        unlink($path . $val);
                    }
                }
            }
        }
    }

    /**
     * 文件夹、文件拷贝
     *
     * @param string $src 来源文件夹、文件
     * @param string $dst 目的地文件夹、文件
     * @return bool
     */
    public static function copy(string $src = '', string $dst = ''): bool
    {


        if (empty($src) || empty($dst)) {
            return false;
        }
        if (is_file($src)) {
            return copy($src, $dst);
        }

        $dir = opendir($src);
        self::mkDir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);

        return true;
    }

    /**
     * 创建文件夹
     *
     * @param string $path 文件夹路径
     * @param bool $recursive 是否递归创建
     * @return bool
     */
    public static function mkDir(string $path, bool $recursive = true): bool
    {
        clearstatcache();
        if (!is_dir($path)) {
            return @mkdir($path, 0777, $recursive);
        }

        return true;
    }

    static function zip($dir, $dst)
    {
        $zip = new ZipArchive();
        if ($zip->open($dst, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            self::addFile2Zip($dir, $zip, $dir); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        }
    }

    static function traverseDirectory($dir, $callback)
    {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' && $file != '..' && substr($file, 0, 1) !== ".") {
                        $path = $dir . DIRECTORY_SEPARATOR . $file;
                        if (is_dir($path)) {
                            self::traverseDirectory($path, $callback);
                        } else {
                            call_user_func($callback, $path);
                        }
                    }
                }
                closedir($dh);
            }
        }
    }


    private static function addFile2Zip($path, ZipArchive $zip, $replace)
    {
        $handler = opendir($path); //打开当前文件夹由$path指定。
        while (($filename = readdir($handler)) !== false) {
            if (strpos($filename, ".") !== 0) {//文件夹文件名字为'.'和‘..'，不要对他们进行操作
                if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
                    self::addFile2Zip($path . "/" . $filename, $zip, $replace);
                } else { //将文件加入zip对象
                    $zip->addFile($path . "/" . $filename);
                    $zip->renameName($path . "/" . $filename, str_replace($replace, "", $path) . DIRECTORY_SEPARATOR . $filename);
                }
            }
        }
        @closedir($handler);
    }

}
