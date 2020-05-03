<?php

namespace app\controller\shop;

use app\lib\speed\mvc\Controller;

class BaseController extends Controller
{
    public $layout = "layout";


    function init()
    {
        header("Content-type: text/html; charset=utf-8");
        session_start();
    }
} 