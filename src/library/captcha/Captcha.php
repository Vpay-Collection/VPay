<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\captcha;


use cleanphp\App;
use cleanphp\base\Session;
use cleanphp\base\Variables;
use cleanphp\file\Log;

class Captcha
{

    private $result;

    function getCode()
    {
        return $this->result;
    }

    function verify($scene, $code): bool
    {
        $this->result = Session::getInstance()->get($scene);
        Session::getInstance()->delete($scene);
        Log::record("captcha", $scene);
        Log::record("captcha", $code);
        Log::record("captcha", $this->result);
        return $this->result != null && intval($code) === intval($this->result);
    }


    function create($scene)
    {
        $image = imagecreate(200, 100);
        imagecolorallocate($image, 0, 0, 0);
        for ($i = 0; $i <= 9; $i++) {
            // 绘制随机的干扰线条
            imageline($image, rand(0, 200), rand(0, 100), rand(0, 200), rand(0, 100), $this->color($image));
        }

        for ($i = 0; $i <= 100; $i++) {
            // 绘制随机的干扰点
            imagesetpixel($image, rand(0, 200), rand(0, 100), $this->color($image));
        }

        $str = $this->code($scene);//获取验证码
        for ($i = 0; $i < 4; $i++) {
            // 逐个绘制验证码中的字符
            imagettftext($image, rand(20, 38), rand(0, 30), $i * 50 + 25, rand(30, 70), $this->color($image), Variables::getLibPath('captcha', 'fonts', 'Bitsumishi.ttf'), $str[$i]);
        }
        @header('Content-type:image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
        App::exit('输出验证码图片');
    }

    private function color($image)
    {
        // 生成随机颜色
        return imagecolorallocate($image, rand(127, 255), rand(127, 255), rand(127, 255));
    }

    private function code($scene): string
    {
        // 验证码中所需要的字符
        $chars = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
        $chars2 = ["+", "-"];
        $_1 = $chars[rand(0, sizeof($chars) / 2)];
        $_2 = $chars[rand(sizeof($chars) / 2, sizeof($chars) - 1)];

        $opt = $chars2[rand(0, 1)];

        $str = $_1 . $opt . $_2 . "=";

        if ($opt == "+") {
            $this->result = $_1 + $_2;
        } else {
            $this->result = $_1 - $_2;
        }
        Session::getInstance()->set($scene, $this->result, 300);
        return $str;
    }

}