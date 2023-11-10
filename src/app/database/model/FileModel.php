<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\model
 * Class FileModel
 * Created By ankio.
 * Date : 2023/9/26
 * Time : 11:26
 * Description :
 */

namespace app\database\model;

use library\database\object\Model;

class FileModel extends Model
{
    public string $name = "";
    public int $date = 0;//上传时间
    public string $path = "";//文件路径
    public int $count = 0;//引用计数
    public int $timeout = 0;
    public string $hash = "";//文件哈希
    public string $link = "";//文件关联的Key
}