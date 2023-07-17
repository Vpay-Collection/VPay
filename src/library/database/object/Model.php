<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\base
 * Class Model
 * Created By ankio.
 * Date : 2022/11/14
 * Time : 23:35
 * Description :
 */

namespace library\database\object;

use cleanphp\base\ArgObject;

abstract class Model extends ArgObject
{
    private bool $fromDb = false;

    public int $id = 0;
    public function __construct(array $item = [], $fromDb = false)
    {
        $this->fromDb = $fromDb;
        parent::__construct($item);
    }

    public function onParseType(string $key, mixed &$val, mixed $demo): bool
    {
        if (is_bool($demo)) {
            $val = ($val === "1" || $val === 1 || $val === "true" || $val === "on" || $val === true);
        }
        if($this->fromDb && is_string($val) && (is_array($demo)||is_object($demo))){
            $val = __unserialize($val);
        }

        if ($this->fromDb && is_string($demo) && !$this->inNofilter($key)) {
            $val = htmlspecialchars($val);
        }
        return true;
    }

    /**
     * 是否为不不要过滤的字段
     * @param $key
     * @return bool
     */
    private function inNofilter($key):bool{
        return in_array($key,$this->getNofilter());
    }

    /**
     * 获取不需要过滤的字段
     * @return array
     */
    public function getNofilter(): array
    {
        return [];
    }

    /**
     * 获取唯一字段
     * @return array
     */
    public function getUnique():array{
        return [];
    }

    /**
     * @return bool
     */
    public function isFromDb(): bool
    {
        return $this->fromDb;
    }

    /**
     * 获取主键
     * @return SqlKey
     */
    public function getPrimaryKey(): SqlKey
    {
        return new SqlKey('id',0,true);
    }


    public function onToArray($key, &$value)
    {
        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
        if(is_array($value)||is_object($value)){
            $value = __serialize($value);
        }
    }




}