<?php
/**
 * Item.php
 * Created By Dreamn.
 * Date : 2020/3/25
 * Time : 11:21 上午
 * Description :
 */
namespace app\model;
use app\lib\speed\mvc\Model;


class Item extends Model
{


    public function __construct()
    {
        parent::__construct("pay_shop");
    }
    public function get(){
        return $this->selectAll();
    }
    public function getList($page=null, $limit=null){

        $arr= null;
        if($page!=null&&$limit!=null)
            $arr= array($page, $limit);
        return $this->selectAll(null, "id asc", "*", $arr);
    }
    public function add($val){
        return $this->insert($val);
    }
    public function setOption($id,$k,$v){
        return $this->update(array('id'=>$id),array($k=>$v));
    }
    public function del($id){
        $this->delete(array('id'=>$id));
    }

    public function getOne($id)
    {
        return $this->select(array('id'=>$id));
    }

    public function set($id, $arr)
    {
        return $this->update(array('id'=>$id),$arr);
    }
}