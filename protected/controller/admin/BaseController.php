<?php

/*
 * 后台基类，所有程序的基础
 * */
namespace controller\admin;

use lib\speed\mvc\Controller;
use lib\speed\Speed;
use model\User;

class BaseController extends Controller
{
    public $layout = "";//模板文件

    public static function err404($module, $controller, $action, $msg)
    {
        header("HTTP/1.0 404 Not Found");
        $obj = new Controller();
        $obj->display("error");
        exit;
    }

    function init()
    {
        session_start();
        header("Content-type: text/html; charset=utf-8");
        $this->allow();
    }
    private function allow(){
        $reg=array(
            'admin'=>array(
                'main'=>array('login'=>'','key'=>'','index'=>''),

            )
        );
        $user=new User();
        if(!isset($reg[strtolower(Speed::arg('m'))][strtolower(Speed::arg('c'))][strtolower(Speed::arg('a'))])){
            //校验是否有token
            if(!$user->isLogin(Speed::arg('token'))) {
                $this->jump( Speed::url('main', 'index'). "/#login");
            }
        }elseif($user->isLogin(Speed::arg('token'))&&!isset($reg[strtolower(Speed::arg('m'))][strtolower(Speed::arg('c'))][strtolower(Speed::arg('a'))])){
            //var_dump(Speed::arg('m'),Speed::arg('c'),Speed::arg('a'));
            $this->jump(Speed::url('admin/main','index'));
        }


    }

}
