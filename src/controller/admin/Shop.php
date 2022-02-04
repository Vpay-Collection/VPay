<?php

namespace app\controller\admin;

use app\attach\Image;
use app\core\config\Config;
use app\core\utils\FileUtil;
use app\core\utils\StringUtil;
use app\model\ShopItem;

class Shop extends BaseController
{
    function list(): array
    {
        $app = new \app\model\Shop();
        $res = $app->getAllData(arg("page",1), arg("limit",1500),"*");
        if (!empty($res)) {
            $count = sizeof($res);
            if ($count === 0) return $this->ret(403,"暂无数据",$res,0);
            else return $this->ret(0,"获取成功",$res,$app->getPage()===null?$count:$app->getPage()->getTotalCount());
        } else {
            return $this->ret(403, "暂无数据", $res, 0);
        }
    }
    function listItem(): array
    {
        $app = new ShopItem();
        $res = $app->getAll(arg("page",1), arg("limit",1500),arg("id",1));
        if (!empty($res)) {
            $count = sizeof($res);
            if ($count === 0) return $this->ret(403,"暂无数据",$res,0);
            else return $this->ret(0,"获取成功",$res,$app->getPage()===null?$count:$app->getPage()->getTotalCount());
        } else {
            return $this->ret(403, "暂无数据", $res, 0);
        }
    }
    function delItem(){
        $shopId = arg("shopId");
        $ids = arg("ids");
        $id = explode(",",$ids);
        $app = new ShopItem();
        foreach ($id as $i){
            if($i!=""){
                $app->del($i,$shopId);
            }
        }
        return $this->ret(200);

    }
    function import(): array
    {
        $data = arg("data");
        $list = explode("\n",$data);
        $app = new ShopItem();
        foreach ($list as $item){
            $app->add(arg("shopId"),$item);
        }
        return $this->ret(200);
    }

    function edit(): array
    {
        $app = new \app\model\Shop();
        $title = arg("title");
        $id=arg("id","-1");
        $description=arg("description");
        $code=base64_decode(arg("code"));
        $price=arg("price");
        $msg=arg("msg");
        $params=base64_decode(arg("params"));
        $img=$this->text($title);
        if($id!="-1"){
            $app->set($id,$title,$description,$code,$price,$msg,$params,$img);
        }else{
            $app->add($title,$description,$code,$price,$msg,$params,$img);
        }

        $app->removeUseless($img);


        return $this->ret(200);
    }

    private function text($text="Vpay"): string
    {

        $text = StringUtil::get($text)->sub(0,6);
        $randImg = APP_VIEW."img".DS."img_".rand(1,8).".png";
        // $src = "aeroplane.jpg";
        $image = new Image($randImg);
        //  $source = "logo.png";
        // $image -> waterMarkImage($source, 0, 0, 30);
        $image -> thumb(500, 300);

        $fontPath = APP_VIEW."font".DS."font.ttf";
        // $text = "文字图片水印";
        $image -> waterMarkText(
            $text,
            $fontPath,
            60,
            array(255, 255, 255, 20),
            10,
            150,
            0);
        $file = DS."ui".DS."img".DS.md5($text);
        FileUtil::mkDir(APP_PUBLIC.DS."ui".DS."img".DS);
        $image -> save(APP_PUBLIC.$file);
        return $file.".png";
    }

    function del(): array
    {
        $app = new \app\model\Shop();
        $id=arg("id","");
        if($id!=""){
          $data =  $app->get($id);
          if(!empty($data)){
             $img = APP_PUBLIC.DS. $data[0]["img"];
             FileUtil::delFile($img);
              $app->del($id);
          }

        }
        return $this->ret(200);
    }

    function system(): array
    {
        $pay =  Config::getInstance("pay")->get();
        return $this->ret(200,null,[
            "name"=> $pay["shop"]["name"],
            "state"=> $pay["shop"]["state"],
            "notice"=> $pay["shop"]["notice"]
        ]);
    }

    function systemEdit(): array
    {
        $pay =  Config::getInstance("pay")->get();
        $pay["shop"]["name"] = arg("name");
        $pay["shop"]["state"] = arg("state");
        $pay["shop"]["notice"] = arg("notice");
        Config::getInstance("pay")->setAll($pay);
        return $this->ret(200,"保存成功");
    }
}