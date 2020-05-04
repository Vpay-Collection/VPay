<?php
/**
 * 生成带LOGO图片的二维码 演示
 * @FileName: demo.php
 * @Author: tekin
 * @QQ: 3316872019
 * @Email: tekintian@gmail.com
 * @Supported: http://dev.yunnan.ws/
 * @Date:   2017-05-28 13:21:38
 * @Last Modified 2017-05-28
 */
namespace app\lib\phpqrcode;
include_once 'phpqrcode.php';

class Code{
    static function create($data,$errorCorrectionLevel='L',$matrixPointSize=10,$logo=false){
       //二维码数据
        //纠错级别：L、M、Q、H
        //二维码图片的大小，单位：点， 1到10
        \QRcode::png($data, APP_TMP.'qr.png', $errorCorrectionLevel, $matrixPointSize, 2); //不带Logo二维码的文件名
        $QR = APP_TMP.'qr.png';
        if ($logo !== false) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
// 输出图像到浏览器
        header('Content-Type: image/png');
        imagepng($QR); //带Logo二维码的文件名
    }
}
