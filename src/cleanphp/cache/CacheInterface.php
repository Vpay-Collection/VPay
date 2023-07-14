<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Cache.php
 * Created By Dreamn.
 * Date : 2020/5/17
 * Time : 6:47 下午
 * Description :  缓存类
 */

namespace cleanphp\cache;

/**
 * Class Cache
 * @package cleanphp\cache
 * Date: 2020/11/21 11:33 下午
 * Author: ankio
 * Description: 缓存类
 */
interface CacheInterface
{

    /**
     * Cache初始化
     * @param int $exp_time 超时时间，单位为秒
     * @param string $path 缓存路径
     */
    public static function init(int $exp_time = 0, string $path = '');


    /**
     * 删除缓存
     * @param string $key
     */
    public function del(string $key);


    /**
     * 设置缓存
     * @param string $key
     * @param mixed $data
     */
    public function set(string $key, mixed $data);

    /**
     * 获取缓存值
     * @param mixed $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * 清空缓存
     */
    public function empty();

    /**
     * 设置数据
     * @param int $exp_time
     * @param string $path
     * @return CacheInterface
     */
    function setData(int $exp_time, string $path): CacheInterface;
}
