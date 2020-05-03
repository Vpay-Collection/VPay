<?php
namespace app\controller\index;


class MainController extends BaseController
{
    // 首页
    function actionIndex(){
        $this->display('main_index_install');

    }


}