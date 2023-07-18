<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: cleanphp\process
 * Class AsyncObject
 * Created By ankio.
 * Date : 2023/3/20
 * Time : 14:53
 * Description :
 */

namespace cleanphp\process;

class AsyncObject
{
    const START = 0;
    const END = 1;
    const WAIT = 2;
    public string $key = "";
    public int $timeout = 0;//超时时间
    public  $function ;
    public int $state = 0;
}