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

class Shop extends Model
{
    public function __construct()
    {
        parent::__construct("pay_shop");
    }
    //
    function add($title,$description,$code,$price,$msg,$params,$img){
        $this->insert(SQL_INSERT_NORMAL)->keyValue(["title"=>$title,"description"=>$description,"code"=>$code,"isCode"=>$code==null?0:1,"price"=>$price,"msg"=>$msg,"params"=>$params,"img"=>$img])->commit();
    }

    function set($id,$title,$description,$code,$price,$msg,$params,$img){
        $this->update()->where(["id"=>$id])->set(["title"=>$title,"description"=>$description,"code"=>$code,"price"=>$price,"isCode"=>$code==null?0:1,"msg"=>$msg,"params"=>$params,"img"=>$img])->commit();
    }

    function get($id,$param="*"){
        return $this->select($param)->where(["id"=>$id])->commit();
    }
    function getAllData(int $page, int $limit,$param="isCode,price,id,title,description,img"){
        $data = $this->select($param)->page($page, $limit)->commit();
        if(!empty($data)){
            $shopItem = new ShopItem();
            for($i=0;$i<sizeof($data);$i++){
                $item = $data[$i];
                $data[$i]["count"] = $shopItem->getList($item["id"]);
            }
            return $data;
        }
        return null;

    }

    function del($id){
        $this->delete()->table("pay_shop_item")->where(["shopId"=>$id])->commit();
        return $this->delete()->where(["id"=>$id])->commit();
    }
    /**
     * 后台响应，获取所有的订单
     * @param $page int 第几页
     * @param $limit int 每页数量
     * @return array|int
     */
    public function getAll(int $page, int $limit,$param="*")
    {
        return $this->select($param)->page($page, $limit)->orderBy("id desc")->commit();
    }

}