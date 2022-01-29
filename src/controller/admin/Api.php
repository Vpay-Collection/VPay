<?php

namespace app\controller\admin;

use MGQrCodeReader\MGQrCodeReader;

class Api extends BaseController
{
    function qr(){
        $qr=new  MGQrCodeReader();
        if(arg('base64')!==null){
            try{
                file_put_contents(APP_CACHE.'/upload.png',base64_decode(urldecode(arg('base64'))));
                $data=$qr->read(APP_CACHE.'/upload.png');
            }catch(\Exception $e){
                return $this->ret(403, "二维码解码失败", "");
            }
            return $this->ret(200,  "成功",$data);
        }

        if (isset($_FILES["file"])) {
            $local=$_FILES["file"]["tmp_name"];
        } else {
            return $this->ret(403,  "图片上传失败",  "");
        }

        try{
            $data=$qr->read($local);
        }catch(\Exception $e){
            return $this->ret(403,  "二维码解码失败",  "");
        }
        return $this->ret(200,  "成功", $data);
    }

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