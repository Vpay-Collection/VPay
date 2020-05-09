<?php

namespace app\controller\admin;

use app\lib\speed\mvc\Controller;
use app\model\User;

class BaseController extends Controller
{
    public $layout = "layout";
    public $version='2.3';

    function init()
    {
        header("Content-type: text/html; charset=utf-8");
        session_start();
        $user=new User();
        $arr=array(
            'main'=>array('login'=>'','logout'=>'','receive'=>'','captcha'=>'','key'=>'')
        );
        if(!$user->isLogin(arg('token'))&&!isset($arr[strtolower(arg('c'))][strtolower(arg('a'))])){
           $this->jump(url('admin/main','login'));
        }elseif ($user->isLogin(arg('token'))&&arg('a')!=='key'&&arg('a')!=='logout'&&isset($arr[strtolower(arg('c'))][strtolower(arg('a'))])){
            $this->jump(url('admin/main','index'));
        }
    }
} 