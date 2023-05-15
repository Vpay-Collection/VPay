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

use cleanphp\objects\ArgObject;
use cleanphp\objects\StringBuilder;

abstract class Model extends ArgObject
{
    private bool $fromDb = false;

    public function __construct(array $item = [], $fromDb = false)
    {
        $this->fromDb = $fromDb;
        parent::__construct($item);
    }

    public function onParseType(string $key, &$val, $demo)
    {
        if (is_bool($demo)) {
            $val = ($val === "1" || $val === 1 || $val === "true" || $val === true);
        }
        if ($this->fromDb && is_string($demo) && !(new StringBuilder($key))->endsWith("nofilter")) {
            $val = htmlspecialchars($val);
        }
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
     * @return array|SqlKey
     */
    abstract function getPrimaryKey();


    public function onToArray($key, &$value)
    {
        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
    }


}