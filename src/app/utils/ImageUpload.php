<?php
/*
 *  Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: app\utils
 * Class ImageUpload
 * Created By ankio.
 * Date : 2023/1/26
 * Time : 15:56
 * Description : 图片上传工具类
 */

namespace app\utils;

use cleanphp\base\Variables;
use cleanphp\file\File;
use cleanphp\objects\StringBuilder;
use library\upload\Upload;
use library\upload\UploadException;
use library\upload\UploadFile;

class ImageUpload
{

    private string $temp = "";
    private string $dir = "";
    private string $dir_name = "";

    public function __construct($dir)
    {
        $this->dir_name = $dir;
        $this->dir = Variables::getStoragePath("uploads", $dir . DS);
        $this->temp = Variables::getStoragePath("uploads", "temp" . DS);
        File::mkdir($this->dir, 0777);
        File::mkdir($this->temp, 0777);
        $this->delLast1Hour();
    }

    /**
     * 删除过期的上传文件
     * @return void
     */
    private function delLast1Hour()
    {
        $time = 3600;
        foreach (scandir($this->temp) as $d) {
            if ((new StringBuilder($d))->startsWith('.')) continue;
            if (time() > (filemtime($this->temp . DS . $d) + $time)) {
                unlink($this->temp . DS . $d);
            }
        }
    }

    /**
     * 使用图片
     * @param $image
     * @return mixed|string
     */
    public function useImage($image)
    {

        if ((new StringBuilder($image))->startsWith("http")) {
            return $image;//表示已经处理
        }
        if ($image === "") return $image;
        $physics = $this->dir . $image;
        $temp = $this->temp . $image;
        if (is_file($temp)) {
            File::copy($temp, $physics);
            File::del($temp);
        }
        return url("index", "main", "image", ["type" => $this->dir_name, "file" => $image]);
    }

    /**
     * 删除图片
     * @param $image
     * @return void
     */
    public function delImage($image)
    {
        if ((new StringBuilder($image))->startsWith("http")) {
            $image = substr($image, strrpos($image, "/") + 1);
        }
        $physics = $this->dir . DS . $image;
        File::del($physics);
    }

    /**
     * 调用文件上传
     * @param $filename
     * @return bool
     */
    public function upload(&$filename): bool
    {
        $upload = new Upload();
        $upload->path = $this->temp;
        $upload->max_size = 1024 * 10;
        try {
            $upload->upload(function (UploadFile &$file) {
                //图片压缩
                      ImageCompress::compress($file->tmp_name);
                return false;
            });
            $files = $upload->getUploadFiles()[0];
            $filename = $files->new_name . "." . $files->type;
            //  $filename = url("index", "main", "image", ["type" => "temp", "file" => $filename]);
            return true;
        } catch (UploadException $e) {
            $filename = $e->getMessage();
            return false;
        }
    }

    public function exist(string $image): bool
    {
        if ((new StringBuilder($image))->startsWith("http")) {
            $image = substr($image, strrpos($image, "/") + 1);
            return file_exists($this->dir . DS . $image);
        }
        return false;
    }

}