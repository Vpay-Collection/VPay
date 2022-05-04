<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * Class Main
 * Created By ankio.
 * Date : 2022/2/1
 * Time : 10:06 上午
 * Description :
 */

namespace app\controller\shop;

use app\attach\Email;
use app\core\config\Config;
use app\core\web\Response;
use app\core\web\Session;
use app\lib\pay\Vpay;
use app\model\Shop;
use app\model\ShopItem;

class Main extends BaseController
{
    public function init()
    {
       Session::getInstance()->start();

    }

    function info(): array
    {
        $pay =  Config::getInstance("pay")->get();
        return $this->ret(200,null,[
            "name"=> $pay["shop"]["name"],
            "state"=> $pay["shop"]["state"],
            "notice"=> $pay["shop"]["notice"]
        ]);
    }

    function list(): array
    {
        $app = new Shop();
        $res = $app->getAllData(arg("page", 1), arg("limit", 1500));
        if (!empty($res)) {
            $count = sizeof($res);
            if ($count === 0) return $this->ret(403, "暂无商品售卖呢~", $res, 0);
            else return $this->ret(0, "获取成功", $res, $app->getPage() === null ? $count : $app->getPage()->getTotalCount());
        } else {
            return $this->ret(403, "暂无商品售卖呢~", $res, 0);
        }
    }

    function get(): array
    {
        $app = new Shop();
        $res = $app->get(arg("id", 1), "price,id,title,description,img,params");
        if (!empty($res)) {
            $data = $res[0];
            return $this->ret(0, "获取成功", $data);
        } else {
            return $this->ret(403, "暂无数据");
        }
    }

    /**
     * 异步回调地址，此处用于处理真正的逻辑
     */
    public function Notify(): array
    {
        $Vpay = new Vpay();
        if ($Vpay->PayNotify($_POST)) {//异步回调验证通过
            //业务处理
            $json = json_decode(base64_decode(urldecode(arg("param"))),true);
            //可以自己通知用户
            //$mail = new Email();
           
            $card = "";
                if(isset($json["isCode"])&&$json["isCode"]=="0"&&isset($json["card"])&&isset($json["id"])){
                    $shopItem = new ShopItem();
                    $shopItem->delCard($json["id"],$json["card"]);
                    $card = $json["card"];
                }elseif (isset($json["isCode"])&&$json["isCode"]!="0"){
                    $card = $this->payCode("1",$json);
                }
            
                
                if (isset($json->mail) && Email::isEmail($json->mail)) {
                    $json["card"] = $card;
                    $msg =$this->getMailContent($json["msg"],$json);
                    //发送卡密
                    $mail = new Email();
                    $pay = Config::getInstance ("pay")->get ();
                    $tplData = [
                        "logo" => Response::getAddress () . DS . "ui" . DS . "static" . DS . "img" . DS . "face.jpg",
                        "sitename" => $pay["pay"]["siteName"],
                        "title" => "支付成功",
                        "body" => $this->getMailContent ($$msg, $_POST)
                    ];
    
                    $file = $mail->complieNotify ("#4076c4", "#fff", $tplData["logo"], $tplData["sitename"], $tplData["title"], $tplData["body"]);
                    //  $mail->send($pay["mail"]["receive"], "{$tplData['sitename']}", $file, $tplData['sitename']);
                    $mail->send ($json["mail"], "{$tplData['sitename']}", $file, $tplData['sitename']);
                }
            
            return $this->ret(200);
          //  dump($json,true);

        }
        return $this->ret(403,$Vpay->getErr());
    }

    /**
     * 同步回调，支付完成后的回调地址
     */
    public function Return()
    {
        $Vpay = new Vpay();
      //  dump($_GET);
        if ($Vpay->PayReturn($_GET)) {//回调时，验证通过
            $param = json_decode(base64_decode(urldecode(arg("param"))),true);
           // dump($param);
            Response::msg(false,200,"支付成功",$param["msg"],-1,"/ui/card","返回首页");
        } else {
            Response::msg(true,403,"支付失败","您的支付信息无效！",-1,"/ui/card");
        }

    }

    /**
     * 订单创建
     */
    public function Create()
    {
        //此处仅需提交id,以及其他附加参数
       $shop = new Shop();
       $shopData = $shop->get(arg("id"));
       if(empty($shopData)){
           return $this->ret(403,"没有该商品");
       }
        $shopData = $shopData[0];

       $shopItem = new ShopItem();
        $args = arg();
        unset($args["m"]);
        unset($args["a"]);
        unset($args["c"]);
        unset($args["token"]);
        $shopItemData = "";
       if($shopData["isCode"]!="1")
           $shopItemData = $shopItem->getOne($shopData["id"]);

      if($shopItemData==null) return $this->ret(403,"该商品已售罄！");

        $price = $shopData['price'];//价格
        $name = $shopData['title'];//商品名称

        $mail = arg('mail');


        $args["isCode"]=$shopData["isCode"];
        $args["title"]=$shopData["title"];
        $args["description"]=$shopData["description"];
        $args["card"]=$shopItemData;
        $args["msg"]=$shopData["msg"];
        $params = json_encode($args);


        if ($mail!==null&&!Email::isEmail($mail))return $this->ret(403,"邮箱填写错误");

        //附加参数为文本型
        $vpay = new Vpay();

        $payId = $vpay->getPayId($price, $params);


        $html = 1;//是否使用自带的支付页面，为0表示不使用自带的支付页面

        $arr["payId"] =$payId;
        $arr["price"] = $price;
        $arr["param"] = $params;
        $arr["explain"] = $name;
        $arr["notifyUrl"] = url("shop","main","notify");
        $arr["returnUrl"] =  url("shop","main","return");


        $result = $vpay->Create($arr, $html);
        if ($result === false) return $this->ret(403,$vpay->getErr());
        else return $this->ret(200,null,$result);

    }

    private function payCode($code,$param){
        $function = '<?php function getKey($arg){'.$code.'}';
        $file = APP_CACHE.DS."tmp_".md5($code).".php";
        file_put_contents($file,$function);
        include_once $file;
        return getKey($param);
    }

    private function getMailContent($msg,$arg){
        foreach ($arg as $key => $value){
            if(is_string($value)){
                $msg = str_replace("{".$key."}",$value,$msg);
            }
        }
        return $msg;
    }

}