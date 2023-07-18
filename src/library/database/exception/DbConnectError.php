<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: library\database\exception
 * Class DbConnectError
 * Created By ankio.
 * Date : 2022/11/18
 * Time : 11:13
 * Description :
 */

namespace library\database\exception;

use cleanphp\file\Log;
use exception;

class DbConnectError extends exception
{
    public function __construct($message, array $error, $tag)
    {
        Log::record($tag, sprintf("数据库连接异常：%s，异常信息：%s", $message, implode(" , ", $error)), Log::TYPE_ERROR);
        parent::__construct($message);
    }
}