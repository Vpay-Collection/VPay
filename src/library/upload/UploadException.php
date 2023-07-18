<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: library\upload
 * Class UploadException
 * Created By ankio.
 * Date : 2022/11/20
 * Time : 22:29
 * Description :
 */

namespace library\upload;


use Exception;

class UploadException extends Exception
{
    protected UploadFile $upload_file;

    public function __construct($message = "", $code = 0, UploadFile $file = null)
    {
        parent::__construct($message, $code);
    }

    /**
     * 获取上传的文件对象
     * @return UploadFile
     */
    public function getUploadFile(): UploadFile
    {
        return $this->upload_file;
    }

}