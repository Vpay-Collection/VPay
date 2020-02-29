<?php
/**
 * RSSController.php
 * User: Dreamn
 * Date: 2020/1/3 23:11
 * Description:
 */

namespace controller\demo;
use includes\Email;
use lib\pay\Vpay;
use lib\speed\Speed;
use model\Config;

class BuyController extends BaseController {

    /**
     * 异步回调地址，此处用于处理真正的逻辑
     */
    public function actionNotify(){
        $Vpay=new Vpay();
        if($Vpay->PayNotify($_GET)) {//异步回调验证通过
            //业务处理
            $json=json_decode(urldecode(Speed::arg("param")));
           //do something
            //可以自己通知用户
            Speed::log(urldecode(Speed::arg("param")));
            $mail=new Email();
            if(isset($json->email)&&Email::isEmail($json->email)){
                //通知用户
                $content=$mail->complieNotify(array('notice1'=>"支付成功",'notice2'=>'您已经成功购买《'.$json->name.'》，站长将在24小时内处理订单','notice3'=>'如有疑问请发邮件到dream@dreamn.cn'));

                $mail->send($json->email,'支付结果通知',$content,'Vpay');

            }
            //通知作者，id是商品ID
            $conf=new Config();
            $mailAddress=$conf->getData('MailRec');
            if(isset($json->id)&&($json->id==2||$json->id==3)&&Email::isEmail($mailAddress)){
                $content=$mail->complieNotify(array('notice1'=>"订单通知",'notice2'=>'用户已经购买《'.$json->name.'》请尽快处理','notice3'=>'用户邮箱：'.$json->email.' 用户留言：'.$json->remark));
                $mail->send($mailAddress,'新的订单',$content,'Vpay');
            }
            echo json_encode(array("state"=>Vpay::Api_Ok,"msg"=>"okok"));
        }else {
            //没有通过sign验证或者这笔订单异常
            echo json_encode(array("state" => Vpay::Api_Err, "msg" => $Vpay->getErr()));
        }
    }

    /**
     * 同步回调，支付完成后的回调地址
     */
    public function actionReturn(){
        $Vpay=new Vpay();
        if($Vpay->PayReturn($_GET)){//回调时，验证通过
            setcookie('token','');
            $this->result="";
            $this->result.= "支付成功！！！<br>";
            $this->result.= "后台订单状态必须为“订单已确认”才是真的成功了，否则是失败的<br>";
            $this->result.= "此处的是同步回调，这里不要将数据插入数据库，因为是否支付是没有验证的，数据入库部分请放到异步回调，当你收到钱时，app会推送收钱信息到后台，后台会向该程序发送已收钱的请求<br>";
            $this->result.= "商户订单号：" . $_GET['payId'] . "<br>自定义参数：" . urldecode($_GET['param']) . "<br>支付方式：" . $_GET['type'] . "<br>订单金额：" . $_GET['price'] . "<br>实际支付金额：" . $_GET['reallyPrice'];
        }else{
            //没有通过sign验证
            $this->result='<h4 class="text-center">'.$Vpay->getErr().'<br/></h4>';
        }

    }

    /**
     * 订单创建
     */
    public function actionCreate(){
        $goodlist=array(
            '1'=>array('name'=>'0.1支付测试','price'=>0.1),
            '2'=>array('name'=>'收费远程安装','price'=>60),
            '3'=>array('name'=>'付费咨询','price'=>10)
        );

        $good=Speed::arg('payGood');
        if(!isset($goodlist[$good]))$this->tips('没有该商品！',Speed::url('demo/main','index'));

        $price=$goodlist[$good]['price'];//价格
        $name=$goodlist[$good]['name'];//商品名称

        $email=Speed::arg('email');
        if(!Email::isEmail($email))$this->tips('邮箱输入错误！',Speed::url('demo/main','index'));
        $remark=Speed::arg('remark');

        $param=urlencode(json_encode(array('name'=>$name,'email'=>$email,'remark'=>$remark,'id'=>$good)));
        //附加参数为文本型
        $vpay=new Vpay();

        $payId=$vpay->getPayId($price,$param);

        $type=intval(Speed::arg('payType'))===1?1:2;

        $html=1;//是否使用自带的支付页面，为0表示不使用自带的支付页面

        $arg["payId"]=$payId;
        $arg["price"]=$price;
        $arg["param"]=$param;
        $arg["type"]=$type;

        $result=$vpay->Create($arg,$html);
        if($result===false) $this->tips($vpay->getErr(),Speed::url('demo/main','index'));
        else echo $result;

    }




}