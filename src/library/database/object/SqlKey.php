<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\database\object
 * Class PrimaryKey
 * Created By ankio.
 * Date : 2022/11/15
 * Time : 20:59
 * Description :
 */

namespace library\database\object;

class SqlKey
{
    const TYPE_INT = 0;
    const TYPE_FLOAT = 1;
    const TYPE_TEXT = 2;
    const TYPE_BOOLEAN = 3;
    const TYPE_ARRAY = 4;

    public string $name;//键名
    public int $type;//类型
    public bool $auto;//是否自增
    public int $length;//字符长度
    public $value;

    /**
     * @param string $name 键名
     * @param mixed|null $default_value 默认参数
     * @param int $length 字符长度，仅默认参数类型为{@link string}生效
     * @param bool $auto 是否自增，仅默认参数类型为{@link int}生效
     */
    public function __construct(string $name, mixed $default_value = null, bool $auto = false, int $length = 0)
    {
        $this->name = $name;
        $this->auto = false;
        $this->length = 0;
        $this->value = $default_value;
        if (is_int($default_value)) {
            $this->type = self::TYPE_INT;
            $this->auto = $auto;
        } elseif (is_string($default_value)) {
            $this->type = self::TYPE_TEXT;
            $this->length = $length;
        } elseif (is_bool($default_value)) {
            $this->type = self::TYPE_BOOLEAN;
        } elseif (is_float($default_value)) $this->type = self::TYPE_FLOAT;
        elseif (is_double($default_value)) $this->type = self::TYPE_FLOAT;
        elseif(is_array($default_value)||is_object($default_value)){
            $this->type = self::TYPE_ARRAY;
        }else $this->type = self::TYPE_TEXT;


    }
}