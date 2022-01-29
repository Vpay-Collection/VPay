<?php

namespace app\core\utils;


/**
 * Class File
 * Created By ankio.
 * Date : 2022/1/12
 * Time : 8:09 下午
 * Description : 文件工具类
 */
class FileUtil {
    public static function delFile($fileName){
        if(is_file($fileName))unlink($fileName);
    }
    /**
     * 文件夹删除或者文件删除
     * @param $dirname
     * @return bool
     */
    public static function del($dirname): bool
    {
        if (!is_dir($dirname)) {
            if(is_file($dirname))
                return  unlink($dirname);
            else
                return false;
        }
        $handle = opendir($dirname); //打开目录
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                //排除"."和"."
                $dir = $dirname .'/' . $file;
                is_dir($dir) ? self::del($dir) : unlink($dir);
            }
        }
        closedir($handle);
        return rmdir($dirname);
    }

    /**
     * 清空一个文件夹
     * @param string $path
     */
    static function cleanDir(string $path)
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
                        self::cleanDir($path . $val . '/');
                        //目录清空后删除空文件夹
                          @rmdir($path.$val.'/');
                    } else {
                        //如果是文件直接删除
                        unlink($path . $val);
                    }
                }
            }
        }
    }
    /**
     * 文件夹文件拷贝
     *
     * @param string $src 来源文件夹
     * @param string $dst 目的地文件夹
     * @return bool
     */
    public static function copyDir(string $src = '', string $dst = ''): bool
    {
        if (empty($src) || empty($dst))
        {
            return false;
        }

        $dir = opendir($src);
        self::mkDir($dst);
        while (false !== ($file = readdir($dir)))
        {
            if (($file != '.') && ($file != '..'))
            {
                if (is_dir($src . '/' . $file))
                {
                    self::copyDir($src . '/' . $file, $dst . '/' . $file);
                }
                else
                {
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
     * @param int $mode 访问权限
     * @param bool $recursive 是否递归创建
     * @return bool
     */
    public static function mkDir(string $path = '', int $mode = 0777, bool $recursive = true): bool
    {
        clearstatcache();
        if (!is_dir($path))
        {
            mkdir($path, $mode, $recursive);
            return chmod($path, $mode);
        }

        return true;
    }


    /**
     * 判断是否符合命名规则
     * @param $name
     * @return bool
     */
    public static function isName($name): bool
    {
        $isMatched = preg_match_all('/^[0-9a-zA-Z_]+$/', $name);
        if($isMatched)return true;
        else {

            return false;
        }
    }





}
