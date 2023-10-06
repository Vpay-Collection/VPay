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
    public $name = "";
    public $date = 0;//上传时间
    public $path  = "";//文件路径
    public $count = 0;//引用计数
    public $timeout = 0;
}