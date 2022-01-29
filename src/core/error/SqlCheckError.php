<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\error;

/**
 * Class SqlCheckError
 * Created By ankio.
 * Date : 2022/1/12
 * Time : 7:23 下午
 * Description :
 */
class SqlCheckError
{
    /**
     * @param string $sql
     * @param string|array $message
     */
    public function __construct(string $sql,  $message = [])
    {
        $errorInfo = "\nSQL语句预编译失败，存在以下问题：\n";
        $errorInfo.= "\nSQL编译语句为：\n".$sql."\n";
        if(is_array($message)&&sizeof($message)==3){
            $errorInfo.= "\n错误信息：\n".$message[2]."\n";
        }
        Error::err($errorInfo);
    }

}