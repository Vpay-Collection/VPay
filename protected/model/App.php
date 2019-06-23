<?php
/*
 * app模块，和应用相关
 * */
class App extends Model
{

    public function __construct($table_name = "pay_appication")
    {
        parent::__construct($table_name);
    }
    //添加app
    public function insert($app_name, $return_url, $notify_url, $connect_key)
    {
        $this->insert_ignore(array(
            "app_name" => $app_name, "return_url" => $return_url, "notify_url" => $notify_url, "connect_key" => $connect_key
        ));
    }
    //获得app列表的信息
    public function get($page, $limit)
    {
        return $this->findAll(null, "id asc", "*", array($page, $limit));
    }
    //获得app信息
    public function getData($id, $parm = "*")
    {
        return $this->find(array("id" => $id), "", $parm);
    }
    //删除app
    public function del($id)
    {
        $this->delete(array("id" => $id));
    }
}