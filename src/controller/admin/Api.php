<?php

namespace app\controller\admin;


class Api extends BaseController
{


    function logo(){
        if (isset($_FILES["file"])) {
            $local=$_FILES["file"]["tmp_name"];
        } else {
           return $this->ret(403,  "图片上传失败",  "");
        }
        file_put_contents(APP_IMG.DS.'qrLogo.png',file_get_contents($local));
        return $this->ret(200, "上传成功", '');
    }

}