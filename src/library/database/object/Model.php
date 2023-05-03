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

    /*  function copy($new): Model
      {
          $ret = get_object_vars($new);
          $old_ = get_object_vars($this);
          $cls = new (get_class($this));
          foreach ($ret as $key => $value) {
              if(in_array($key,$old_) && $cls->$key !== $value){
                  $this->$key = $value;
              }
          }
          return $this;
      }*/

}