<?php

class App extends Model
{

    public function __construct($table_name = "pay_appication")
    {
        parent::__construct($table_name);
    }

    public function insert($app_name, $return_url, $notify_url, $connect_key)
    {
        $this->insert_ignore(array(
            "app_name" => $app_name, "return_url" => $return_url, "notify_url" => $notify_url, "connect_key" => $connect_key
        ));
    }

    public function get($page, $limit)
    {
        return $this->findAll(null, "id asc", "*", array($page, $limit));
    }

    public function getData($id, $parm = "*")
    {
        return $this->find(array("id" => $id), "", $parm);
    }

    public function del($id)
    {
        $this->delete(array("id" => $id));
    }
}