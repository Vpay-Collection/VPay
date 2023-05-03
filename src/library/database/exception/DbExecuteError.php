<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
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

use cleanphp\App;
use cleanphp\file\Log;
use Exception;

class DbExecuteError extends Exception
{
    public function __construct($message = "")
    {
        App::$debug && Log::record("Database", $message);
        parent::__construct($message);
    }
}