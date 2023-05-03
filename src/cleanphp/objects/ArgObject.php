<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\objects
 * Class ArgObject
 * Created By ankio.
 * Date : 2022/11/14
 * Time : 21:08
 * Description :
 */

namespace cleanphp\objects;

class ArgObject
{
    /**
     * 将数组转换为对象，使其更具表现力
     * @param array|null $item array 数组
     */
    public function __construct(?array $item = [])
    {
        if (empty($item)) return;
        foreach (get_object_vars($this) as $key => $val) {
            $data = $val;
            if (isset($item[$key])) {
                $data = $item[$key];
            }
            $data = parse_type($val, $data);
            $this->onParseType($key, $data, $val);
            $this->$key = $data;
        }
    }

    /**
     * 当进行数组转换为object的时候，可重写该方法进行数据校验
     * @param string $key 对象属性名
     * @param mixed &$val 对象属性值，传入的是地址，直接修改即可
     * @param mixed $demo 默认属性值
     */
    public function onParseType(string $key, &$val, $demo)
    {
    }

    /**
     * 转化为数组
     * @return array
     */
    public function toArray(): array
    {
        $ret = get_object_vars($this);
        foreach ($ret as $key => &$value) {
            $this->onToArray($key, $value);
        }
        return $ret;
    }

    public function onToArray($key, &$value)
    {

    }

    /**
     * 获取对象hash值
     * @return string
     */
    public function hash(): string
    {
        return md5(implode(",", get_object_vars($this)));
    }
}