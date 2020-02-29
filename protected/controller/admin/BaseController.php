<?php

namespace controller\admin;

use lib\speed\mvc\Controller;
use lib\speed\Speed;
use model\User;

class BaseController extends Controller
{
    public $layout = "layout";


    function init()
    {
        header("Content-type: text/html; charset=utf-8");
        session_start();
        $user=new User();
        $arr=array(
            'main'=>array('login'=>'','logout'=>'','receive'=>'','captcha'=>'','key'=>'')
        );
        if(!$user->isLogin(Speed::arg('token'))&&!isset($arr[strtolower(Speed::arg('c'))][strtolower(Speed::arg('a'))])){
           $this->jump(Speed::url('admin/main','login'));
        }elseif ($user->isLogin(Speed::arg('token'))&&Speed::arg('a')!=='key'&&isset($arr[strtolower(Speed::arg('c'))][strtolower(Speed::arg('a'))])){
            $this->jump(Speed::url('admin/main','index'));
        }
    }
} 