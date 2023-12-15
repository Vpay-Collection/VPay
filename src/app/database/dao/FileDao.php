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
use cleanphp\base\EventManager;
use cleanphp\base\Route;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
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
            if ($file->count < 0) $file->count = 0;
            $file->count += 1;
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
        $file = $this->find(null, ['name' => $this->getFile($filename)]);
        if (!empty($file)) {
            if ($file->count < 0) $file->count = 1;
            $file->count -= 1;
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
        $data_1 = $this->select("path", "id", "count", "name")->where(["count" => 0, "date < " . strtotime("-1 days")])->commit(false);
        $data_2 = $this->select("path", "id", "count", "name")->where(['timeout < ' . time(), 'timeout<>0'])->commit(false);

        /**
         * @var $item FileModel
         */
        foreach (array_merge($data_1, $data_2) as $item) {
            EventManager::trigger("__deleteTimeoutFile__", $item);
            if ($item['count'] === 0) {
                File::del($item['path']);
                $this->delete()->where(['id' => $item['id']])->commit();
            } else {
                $this->update()->set($item)->where(['id' => $item['id']])->commit();
            }
        }
        $cache = Cache::init(7200, Variables::getCachePath());
        if (!$cache->get("file.lock")) {
            $cache->set("file.lock", true);
            foreach (scandir(Variables::getStoragePath('uploads')) as $value) {
                if (in_array($value, [".", ".."])) continue;
                if ($this->find(null, ['name' => $value])) {
                    continue;
                }
                $data = new FileModel();
                $data->count = 0;
                $data->path = Variables::getStoragePath('uploads', $value);
                $data->name = $value;
                $data->date = time();
                $data->hash = md5_file($data->path);
                $array = $data->toArray();
                EventManager::trigger("__deleteTimeoutFile__", $array);
                if ($array['count'] === 0) {
                    File::del($array['path']);
                } else {
                    $this->insert()->keyValue($array)->commit();
                }
            }

        }

    }

    function getByLink($link)
    {
        return $this->select()->where(['link' => $link])->commit();
    }

    function addExistFile($filename, $timeout = 0, $link = ""): string
    {
        $this->clearNoUsage();
        $model = new FileModel();
        $model->name = $filename;
        $model->date = time();
        $model->timeout = $timeout;

        $model->path = Variables::getStoragePath('uploads', $filename);
        File::mkDir(Variables::getStoragePath('uploads'));
        return $this->insertFile($model, $link, $filename);
    }

    /**
     * @param FileModel $model
     * @param mixed $link
     * @param $filename
     * @return string
     */
    private function insertFile(FileModel $model, mixed $link, $filename): string
    {
        $model->hash = md5_file($model->path);
        $filename = $this->getFilename($link, $model, $filename);
        return url("index", "main", "file", ['file' => $filename]);
    }

    /**
     * @param mixed $link
     * @param FileModel $model
     * @param $filename
     * @return mixed
     */
    private function getFilename(mixed $link, FileModel $model, $filename): mixed
    {
        $model->link = $link;
        $existFile = $this->find(null, ["hash" => $model->hash, "link" => $link]);
        if ($existFile) { //实现图片复用
            if (file_exists($existFile->path)) {
                $filename = $existFile->name;
                if ($model->path !== $existFile->path) {
                    File::del($model->path);
                }
            } else {
                $this->delete()->where(['id' => $existFile->id]);
            }
        } else {
            $this->insertModel($model);
        }
        return $filename;
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
        return $this->insertFile($model, $link, $filename);
    }

    function upload($allow = ['jpg', 'jpeg', 'png', 'gif', 'svg'], $max = 1024 * 1024 * 10, $link = ""): array
    {
        $this->clearNoUsage();
        $upload = new Upload();
        $upload->allow_type = $allow;
        $upload->max_size = $max;
        $upload->path = Variables::getStoragePath('uploads');
        File::mkDir($upload->path);
        try {
            $upload->upload(function (UploadFile &$file) {
                if (in_array($file->type, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
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
            $filename = $this->getFilename($link, $model, $filename);

            return [null, $files, url("index", "main", "file", ['file' => $filename])];
        } catch (UploadException $e) {
            return [$e->getMessage(), null, null];
        }
    }
}