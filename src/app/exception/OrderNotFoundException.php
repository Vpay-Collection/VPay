<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app
 * Class OrderNotFoundException
 * Created By ankio.
 * Date : 2023/3/18
 * Time : 22:09
 * Description :
 */

namespace app\exception;

use Throwable;

class OrderNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}