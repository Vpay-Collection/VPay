<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: library\database\exception
 * Class DataBaseDriverNotFound
 * Created By ankio.
 * Date : 2022/11/16
 * Time : 15:42
 * Description :
 */

namespace library\database\exception;

use cleanphp\file\Log;
use Exception;

class DbFieldError extends Exception
{
    public string $field;

    public function __construct($message = "", $field = "")
    {
        $this->field = $field;
        Log::record("Database Field", $message, Log::TYPE_ERROR);
        parent::__construct($message);
    }
}