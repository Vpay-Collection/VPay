<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class baseError
 * Created By ankio.
 * Date : 2022/1/12
 * Time : 2:56 下午
 * Description : 异常处理
 */
namespace app\core\error;

class AppError
{
    /**
     * App错误处理基类
     * @param string $errorMsg 错误信息
     * @param string $file 报错文件
     * @param string $keyWord 报错关键字
     */
    public function __construct(string $errorMsg,string $file,string $keyWord="")
    {
        Error::err($errorMsg,[["file"=>$file,"line"=>-1,"keyword"=>$keyWord]]);
    }



}