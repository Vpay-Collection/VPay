<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\dao
 * Class FileDao
 * Created By ankio.
 * Date : 2023/9/26
 * Time : 11:31
 * Description :
 */

namespace app\database\dao;

use app\database\model\FileModel;
use app\utils\ImageCompress;
use cleanphp\App;
use cleanphp\base\Route;
use cleanphp\base\Variables;
use cleanphp\file\File;
use library\database\object\Dao;
use library\upload\Upload;
use library\upload\UploadException;
use library\upload\UploadFile;

class FileDao extends Dao
{
    function get($filename): void
    {
        /**
         * @var $file FileModel
         */
        $file = $this->find(null, ['name' => $filename]);
        if (empty($file)) {
            App::exit("file not found", true);
        }
        Route::renderStatic($file->path);
    }

    function use($filename, $replace = null): void
    {


        $filename = $this->getFile($filename);
        $replace = $this->getFile($replace);
        if ($filename === $replace) {
            return;
        }

        if ($replace) {
            $this->del($replace);
        }
        /**
         * @var $file FileModel
         */
        $file = $this->find(null, ['name' => $filename]);
        if (!empty($file)) {
            $file->count = 1;
            $this->updateModel($file);
        }

    }

    private function getFile($name): ?string
    {
        if (!$name) return $name;
        $lastSegment = basename($name);
        if ($lastSegment === "") {
            $lastSegment = $name;
        }
        return $lastSegment;
    }

    function del($filename): void
    {
        /**
         * @var $file FileModel
         */
        $file = $this->find(null, ['name' => $filename]);
        if (!empty($file)) {
            $file->count = 0;
            $this->updateModel($file);
        }
    }

    function exist($file): bool
    {
        $this->clearNoUsage();
        $file = $this->getFile($file);
        $dir = Variables::getStoragePath('uploads', $file);
        return file_exists($dir);
    }

    private function clearNoUsage(): void
    {
        //超过24小时清理门户
        $data_1 = $this->select("path")->where(["count" => 0, "date < " . strtotime("-1 days")])->commit();
        $data_2 = $this->select("path")->where(['timeout < ' . time(), 'timeout<>0'])->commit();
        /**
         * @var $item FileModel
         */
        foreach (array_merge($data_1, $data_2) as $item) {
            File::del($item->path);
            $this->delete()->where(['id' => $item->id]);
        }
    }

    function getByLink($link)
    {
        return $this->select()->where(['link' => $link])->commit();
    }

    function addExistFile($filename, $timeout = 0, $link = "")
    {
        $this->clearNoUsage();
        $model = new FileModel();
        $model->name = $filename;
        $model->date = time();
        $model->timeout = $timeout;

        $model->path = Variables::getStoragePath('uploads', $filename);
        File::mkDir(Variables::getStoragePath('uploads'));
        $model->hash = md5_file($model->path);
        $model->link = $link;
        $existFile = $this->find(null, ["hash" => $model->hash, "link" => $link]);
        if ($existFile) { //实现图片复用
            $filename = $existFile->name;
            File::del($model->path);
        } else {
            $this->insertModel($model);
        }
        return url("index", "main", "file", ['file' => $filename]);
    }

    function add($filename, $file, $timeout = 0, $link = ""): string
    {
        $this->clearNoUsage();
        $model = new FileModel();
        $model->name = $filename;
        $model->date = time();
        $model->timeout = $timeout;

        $model->path = Variables::getStoragePath('uploads', $filename);
        File::mkDir(Variables::getStoragePath('uploads'));
        file_put_contents($model->path, $file);
        $model->hash = md5_file($model->path);
        $model->link = $link;
        $existFile = $this->find(null, ["hash" => $model->hash, "link" => $link]);
        if ($existFile) { //实现图片复用
            $filename = $existFile->name;
            File::del($model->path);
        } else {
            $this->insertModel($model);
        }
        return url("index", "main", "file", ['file' => $filename]);
    }

    function upload($allow = ['jpg', 'jpeg', 'png', 'gif'], $max = 1024 * 1024 * 10, $link = ""): array
    {
        $this->clearNoUsage();
        $upload = new Upload();
        $upload->allow_type = $allow;
        $upload->max_size = $max;
        $upload->path = Variables::getStoragePath('uploads');
        File::mkDir($upload->path);
        try {
            $upload->upload(function (UploadFile &$file) {
                if (in_array($file->type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    if (filesize($file->tmp_name) > 1024 * 1024) {
                        ImageCompress::compress($file->tmp_name);
                    }


                }
                return false;
            });
            $files = $upload->getUploadFiles()[0];
            $filename = $files->new_name . "." . $files->type;
            $model = new FileModel();
            $model->name = $filename;
            $model->date = time();
            $model->path = $upload->path . DS . $filename;
            $hash = md5_file($model->path);
            $model->hash = $hash;
            $model->link = $link;
            $file = $this->find(null, ["hash" => $hash, "link" => $link]);
            if ($file) { //实现图片复用
                $filename = $file->name;
                File::del($model->path);
            } else {
                $this->insertModel($model);
            }

            return [null, $files, url("index", "main", "file", ['file' => $filename])];
        } catch (UploadException $e) {
            return [$e->getMessage(), null, null];
        }
    }
}