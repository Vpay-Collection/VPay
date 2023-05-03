<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
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