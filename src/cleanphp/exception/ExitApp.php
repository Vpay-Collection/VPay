<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: cleanphp\exception
 * Class ExitAppException
 * Created By ankio.
 * Date : 2022/11/10
 * Time : 20:25
 * Description :
 */

namespace cleanphp\exception;

use Error;

class ExitApp extends Error
{
    public function __construct($message = "")
    {
      parent::__construct($message);
    }

}