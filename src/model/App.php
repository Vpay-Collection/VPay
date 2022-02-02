<?php
namespace app\model;

/*
 * app模块，和应用相关
 * */

use app\core\mvc\Model;

class App extends Model
{

    public function __construct()
    {
        parent::__construct('pay_application');
    }

    //添加app
    public function add($app_name, $connect_key)
    {
       return $this->insert(SQL_INSERT_NORMAL)->keyValue([ "app_name" => $app_name,  "connect_key" => $connect_key])->commit();
    }

    //获得app列表的信息
    public function getList($page=1, $limit=15)
    {
        return $this->select()->page($page, $limit)->orderBy("id asc")->commit();
    }
    //获得app信息
    public function getData($id, $param = "*")
    {
        return $this->select($param)->where(["id" => $id])->commit();
    }

    //删除app
    public function del($id)
    {
        $this->delete()->where(["id" => $id])->commit();
    }

    public function get($id)
    {
       return $this->select()->where(["id" => $id])->commit();
    }

    public function set($id, $app_name, $connect_key)
    {
        $this->update()->set([ "app_name" => $app_name,"connect_key" => $connect_key])->where(["id"=>$id])->commit();
    }
}