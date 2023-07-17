<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\database\object;

use cleanphp\base\ArgObject;

/**
 * Package: library\database\object
 * Class DbFile
 * Created By ankio.
 * Date : 2022/11/16
 * Time : 15:24
 * Description : 数据库配置文件模板
 */
class DbFile extends ArgObject
{
    public string $host = "";
    public string $type = "";
    public int $port = 0;
    public string $username = "";
    public string $password = "";
    public string $db = "";
    public string $charset = "utf8";
}