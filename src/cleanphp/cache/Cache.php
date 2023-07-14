<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\cache
 * Class Cache
 * Created By ankio.
 * Date : 2022/11/14
 * Time : 21:24
 * Description :
 */

namespace cleanphp\cache;

use cleanphp\App;
use cleanphp\base\Variables;
use cleanphp\file\File;
use cleanphp\file\Log;

class Cache implements CacheInterface
{

    private static ?CacheInterface $drive = null;
    private string $cache_path = "";
    private int $cache_expire = 3600;

    /**
     * 设置缓存器
     * @param CacheInterface $drive
     * @return void
     */
    public static function setDriver(CacheInterface $drive): void
    {
        Variables::del("__cache__");//以前的对象需要释放
        App::$debug && Log::record("Cache", sprintf("设置缓存器：%s", get_class($drive)));
        self::$drive = $drive;
    }

    /**
     * @inheritDoc
     */
    public function del(string $key): void
    {
        $filename = self::fileName($key);
        File::del($filename);

    }

    /**
     * 获取缓存文件名
     * @param string $key
     * @return string
     */
    private function fileName(string $key): string
    {
        $new  = filter_characters($key);
        if(mb_strlen($new)>100){
            $new = mb_substr($new,0,50).md5($key);
        }
        return $this->cache_path . $new;
    }

    /**
     * @inheritDoc
     */
    public static function init(int $exp_time = 0, string $path = '')
    {
        if (self::$drive) return self::$drive::init($exp_time, $path);
        $cache = Variables::get("__cache__");
        if ($cache === null) $cache = new self();
        $cache->setData($exp_time, $path);
        Variables::set("__cache__", $cache);
        return $cache;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): mixed
    {
        $filename = self::fileName($key);
        if (!file_exists($filename) || !is_readable($filename)) {
            return null;
        }
        if ($this->cache_expire == 0 || time() < (filemtime($filename) + $this->cache_expire)) {
            $data = file_get_contents($filename);
            $result = __unserialize($data);
            if ($result === false) return null;
            return $result;
        } else {
            self::del($key);
            return null;
        }
    }

    function setData(int $exp_time, string $path): CacheInterface
    {
        if ($path === "") $path = Variables::getCachePath();
        if ($path === "temp") $path = sys_get_temp_dir();
        $this->cache_expire = $exp_time;
        $this->cache_path = $path;
        File::mkDir($this->cache_path);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $data): void
    {
        file_put_contents($this->fileName($key), __serialize($data));
    }

    /**
     * @inheritDoc
     */
    public function empty(): void
    {
        App::$debug && Log::record("Cache", "清空所有缓存");
        File::empty($this->cache_path);
    }
}