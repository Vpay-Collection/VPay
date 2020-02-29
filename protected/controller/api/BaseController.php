<?php

/*
 * 后台基类，所有程序的基础
 * */
namespace controller\api;
use lib\speed\mvc\Controller;

class BaseController extends Controller
{
    public $layout = "";//模板文件

    function init()
    {
        //进行校验
        header("Content-type: text/html; charset=utf-8");

    }

}
