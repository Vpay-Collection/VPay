<?php
namespace controller\index;


class MainController extends BaseController
{
    // 首页
    function actionIndex(){
        $admin=true;
        if($admin){
            $this->layout='';
            $this->display('main_index');
        }else{

            $this->display('main_index_install');
        }

    }


}