<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\captcha;


namespace library\captcha;

use cleanphp\App as CleanApp;
use cleanphp\base\Session;

class Captcha
{
    private string $result;

    public function getResult(): string
    {
        return $this->result;
    }

    public function verify(string $scene, string $code): bool
    {
        $this->result = Session::getInstance()->get($scene);
        Session::getInstance()->delete($scene);

        return intval($code) === intval($this->result);
    }

    public function create(string $scene): void
    {
        $image = imagecreate(200, 100);
        imagecolorallocate($image, 0, 0, 0);

        for ($i = 0; $i <= 9; $i++) {
            imageline($image, rand(0, 200), rand(0, 100), rand(0, 200), rand(0, 100), $this->color($image));
        }

        for ($i = 0; $i <= 100; $i++) {
            imagesetpixel($image, rand(0, 200), rand(0, 100), $this->color($image));
        }

        $str = $this->generateCode($scene);

        for ($i = 0; $i < 4; $i++) {
            imagettftext($image, rand(20, 38), rand(0, 30), $i * 50 + 25, rand(30, 70), $this->color($image), CleanApp::getLibPath('captcha', 'fonts', 'Bitsumishi.ttf'), $str[$i]);
        }

        @header('Content-type:image/jpeg');
        imagejpeg($image);
        imagedestroy($image);

        CleanApp::exit('输出验证码图片');
    }

    private function color($image)
    {
        return imagecolorallocate($image, rand(127, 255), rand(127, 255), rand(127, 255));
    }

    private function generateCode(string $scene): string
    {
        $chars = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
        $operators = ["+", "-"];
        $num1 = $chars[rand(0, sizeof($chars) / 2)];
        $num2 = $chars[rand(sizeof($chars) / 2, sizeof($chars) - 1)];
        $operator = $operators[rand(0, 1)];

        $str = $num1 . $operator . $num2 . "=";
        if ($operator == "+") {
            $this->result = $num1 + $num2;
        } else {
            $this->result = $num1 - $num2;
        }

        Session::getInstance()->set($scene, $this->result, 300);
        return $str;
    }
}
