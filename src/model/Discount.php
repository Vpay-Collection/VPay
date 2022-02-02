<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * Class Discount
 * Created By ankio.
 * Date : 2022/1/30
 * Time : 3:46 下午
 * Description :
 */

namespace app\model;

use app\core\mvc\Model;

class Discount extends Model
{
    public function __construct()
    {
        parent::__construct("pay_discount");
    }
    public function add($text,$price,$appid,$timeout){
        $this->insert(SQL_INSERT_NORMAL)->keyValue(["text"=>$text,"price"=>$price,"appid"=>$appid,"timeout"=>$timeout])->commit();
    }

    public function set($id,$text,$price,$appid,$timeout){
        $this->update()->set(["text"=>$text,"price"=>$price,"appid"=>$appid,"timeout"=>$timeout])->where(["id"=>$id])->commit();
    }

    public function del($id){
        $this->delete()->where(["id"=>$id])->commit();
    }

    public function delByText($text){
        $this->delete()->where(["text"=>$text])->commit();
    }
    public function getAll($page, $limit, $appId)
    {
        return $this->select()->where(["appid"=>$appId])->page($page, $limit)->orderBy("id desc")->commit();
    }
    public function getByText($text,$appid){
      return $this->select()->where(["text"=>$text,"appid"=>$appid,"timeout>=:time",":time"=>time()])->commit();
    }
    public function delTimeOut(){
         $this->delete()->where(["timeout<:time",":time"=>time()])->commit();
    }
}