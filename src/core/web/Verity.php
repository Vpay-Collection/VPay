<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/
namespace app\core\web;
/**
 * Class Verity
 * Created By ankio.
 * Date : 2022/1/14
 * Time : 10:35 上午
 * Description : 验证类，可以验证常用输入
 */
class Verity
{

    private static ?Verity $instance = null;
    private string $str = "";
    private string $err = "";

    public static function get($str): Verity
    {
        if (self::$instance == null) {
            self::$instance = new verity();
        }
        self::$instance->setStr($str);
        return self::$instance;
    }

    public function setStr($s)
    {
        $this->str = $s;
    }

    /**
     * 参数格式验证,长度验证
     * @param   $type     integer  验证方式  -1:不需要验证  1:email   2:手机号  3:英文  4:数字  5:汉字  6:url地址  7:身份证  8:QQ
     * @param   $num     integer   规定字符串长度 如果单位是字节，请在使用时自行除以3
     * @return bool 返回数据
     */
    function check(int $type = -1, int $num = 0): bool
    {
        if (!empty($num) && $num > 0) {
            $len = mb_strlen($this->str, 'UTF8');
            if ($len > $num) {
                $this->err = '不能超过' . floor($num) . '个字符';
                return false;
            }
        }

        switch ($type) {
            case 1:
            case 'email':
                $rules = '/^([a-zA-Z0-9_.\-])+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
                break;
            case 2:
            case 'mobile':
                $rules = '/^1[34578]\d{9}$/';
                break;
            case 3:
            case 'en':
                $rules = '/^[a-zA-Z\s]+$/';
                break;
            case 4:
            case 'number':
                $rules = '/^[0-9]*$/';
                break;
            case 5:
            case 'cn':
            $rules = '/^[\u4e00-\u9fa5]*$/';
                break;
            case 6:
            case 'url':
                $rules = '/^http://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?$/';
                break;
            case 7:
            case 'id':
                $rules = '/^((\d{18})|([0-9x]{18})|([0-9X]{18}))$/';
                break;
            case 8:
            case 'qq':
                $rules = '/[1-9][0-9]{4,}/';
                break;
            default:
                $rules = '//';
                break;
        }
        if (preg_match($rules, $this->str)) {

            $this->err = '校验通过';
            return true;
        } else {
            $this->err = '参数不合法';
            return false;
        }

    }

    function getErr(): string
    {
        return $this->err;
    }
}