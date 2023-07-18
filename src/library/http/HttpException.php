<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: library\http
 * Class HttpException
 * Created By ankio.
 * Date : 2022/11/20
 * Time : 21:26
 * Description :
 */

namespace library\http;

use Exception;

class HttpException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}