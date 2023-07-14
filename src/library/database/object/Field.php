<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\database\object
 * Class Field
 * Created By ankio.
 * Date : 2022/11/16
 * Time : 23:38
 * Description :
 */

namespace library\database\object;

use cleanphp\base\Error;

class Field
{
    public array $fields = [];

    /**
     */
    public function __construct(...$fields)
    {
        foreach ($fields as $field) {
            if (!self::isName($field))
                Error::err(sprintf(" %s 字段错误，只允许字母数字下划线", $field), [], "Database Field");
        }
        $this->fields = $fields;
    }

    /**
     * 判断字段是否正常
     * @param $name
     * @return bool
     */
    public static function isName($name): bool
    {
        if (!is_string($name)) return false;

        return preg_match_all('/^[0-9a-zA-Z_.\s]+$/', $name);
    }

    /**
     * 转换为string类型
     * @return string
     */
    public function toString(): string
    {
        if (empty($this->fields)) return "*";
        return implode(",", $this->fields);
    }

    /**
     * 转换为数组
     * @return array
     */
    public function toArray(): array
    {
        return $this->fields;
    }
}