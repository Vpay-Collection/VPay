<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: library\upload
 * Class UploadFile
 * Created By ankio.
 * Date : 2022/11/20
 * Time : 22:55
 * Description :
 */

namespace library\upload;

use cleanphp\base\ArgObject;

class UploadFile extends ArgObject
{
    public string $name = "";//原始文件名
    public string $tmp_name = "";//上传时的随机文件名
    public int $size = 0;//上传的文件大小
    public string $error = "";//上传时的错误
    public string $type = "";//上传的文件类型
    public string $new_name = "";//文件上传的新文件名
    public string $path = "";//文件上传的路径

}