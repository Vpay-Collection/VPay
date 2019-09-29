<?php

/*
 * 后台的主页面，主要用于登录检查等
 * */

class MainController extends BaseController
{
    // 默认首页
    public function actionIndex()
    {
        $this->display("admin/index.html");
    }

    public function actionLogin()
    {//用户登录
        $user = new User();
        if ($user->login(arg("user"), arg("pass"), arg("_t"))) {
            echo json_encode(array("status" => true, "msg" => "登录成功！"));
        } else {

            echo json_encode(array("status" => false, "msg" => "登录失败！"));
        }
    }

    public function actionLogout()
    {//用户登出
        $user = new User();
        $user->logout();
        $this->jump(url('main', 'index'));
    }


}
