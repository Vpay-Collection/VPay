<?php

namespace app\controller\admin;

class App extends BaseController
{
    function list(): array
    {
        $app = new \app\model\App();
        $res = $app->getList(arg("page",1), arg("limit",15));
        if (!empty($res)) {
            $count = sizeof($res);
            if ($count === 0) return $this->ret(403,"暂无数据",$res,0);
            else return $this->ret(0,"获取成功",$res,$app->getPage()===null?$count:$app->getPage()->getTotalCount());
        } else {
            return $this->ret(403, "暂无数据", $res, 0);
        }
    }

    function edit(): array
    {
        $app = new \app\model\App();
        $appName=arg("app_name");
        $id=arg("id","");
        $key=arg("connect_key");
        if($id!=""){
            $app->set($id,$appName,$key);
        }else{
            $app->add($appName,$key);
        }


        return $this->ret(200);
    }

    function del(): array
    {
        $app = new \app\model\App();
        $id=arg("id","");
        if($id!=""){
            $app->del($id);
        }
        return $this->ret(200);
    }
}