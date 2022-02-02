<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Shop
 * Created By ankio.
 * Date : 2022/1/31
 * Time : 8:43 下午
 * Description :
 */

namespace app\model;

use app\core\mvc\Model;

class ShopItem extends Model
{
    public function __construct()
    {
        parent::__construct("pay_shop_item");
    }
    //
    function add($shopId,$code){
        $this->insert(SQL_INSERT_NORMAL)->keyValue(["code"=>$code,"shopId"=>$shopId])->commit();
    }

    function set($id,$shopId,$code){
        $this->update()->where(["id"=>$id])->set(["code"=>$code,"shopId"=>$shopId])->commit();
    }
    function setLock($id){
        $this->update()->where(["id"=>$id])->set(["lockItem"=>time()+5*60*60])->commit();
    }
    function getList($shopId){
        $data = $this->select("count(id) as num")->where(["shopId"=>$shopId,"lockItem<:time",":time"=>time()])->commit();
        if(empty($data)){
            return 0;
        }
        return $data[0]["num"];
    }
    function getOne($shopId){
        $data = $this->select()->where(["shopId"=>$shopId,"lockItem<:time",":time"=>time()])->limit("1")->commit();
        if(empty($data)){
            return null;
        }
        $this->setLock($data[0]["id"]);
        return $data[0]["code"];
    }
    function get($id){
        return $this->select()->where(["id"=>$id])->commit();
    }
    function del($id,$shopId){
        return $this->delete()->where(["id"=>$id,"shopId"=>$shopId])->commit();
    }
    /**
     * 后台响应，获取所有的订单
     * @param $page int 第几页
     * @param $limit int 每页数量
     * @return array|int
     */
    public function getAll(int $page, int $limit,int $shopId)
    {
        return $this->select()->page($page, $limit)->where(["shopId"=>$shopId])->orderBy("id desc")->commit();
    }

}