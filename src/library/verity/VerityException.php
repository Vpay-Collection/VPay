<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\verity
 * Class VerityException
 * Created By ankio.
 * Date : 2022/11/21
 * Time : 00:12
 * Description :
 */

namespace library\verity;

use Exception;

class VerityException extends Exception
{
    public string $key = "";
    public $val = null;

    public function __construct($message = "", $key = "", $val = null)
    {
        $this->key = $key;
        $this->val = $val;
        parent::__construct($message);
    }
}