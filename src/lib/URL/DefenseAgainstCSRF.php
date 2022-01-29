<?php


namespace app\lib\URL;

use app\core\web\Cookie;
use app\core\web\Session;

/**
 * Class DefenseAgainstCSRF
 * @package Security\URLSecurity
 */
class DefenseAgainstCSRF
{
    /**
     * DefenseAgainstCSRF constructor.
     */
    public function __construct()
    {
    }


    /**
     * 验证csrf token
     * @return bool
     */
    public function verifyCSRFToken(): bool
    {
        $csrf=Session::getInstance()->get("csrftoken");
        if($csrf===null)return false;
        $bool=$csrf===Cookie::getInstance()->get("csrftoken");
        Session::getInstance()->set("csrftoken",null);
        return $bool;
    }


    /**
     * @param $session
     * @param $salt
     * @return string
     */
    private function getCSRFToken($session, $salt): string
    {
        $token=md5(md5($session.'|'.$salt).'|'.$salt);
        Cookie::getInstance()->set("csrftoken",$token);
        Session::getInstance()->set("csrftoken",$token,20*60);
        return $token;
    }

    /**
     * 设置csrf token
     * @param $session
     * @return string
     */
    public function setCSRFToken($session): string
    {
        return $this->getCSRFToken($session,time());
    }
}
