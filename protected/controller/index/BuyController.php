<?php
namespace app\controller\index;


use app\includes\Email;
use app\lib\pay\Vpay;
use app\model\Config;
use app\model\Item;

class BuyController extends BaseController
{
    /**
     * 异步回调地址，此处用于处理真正的逻辑
     */
    public function actionNotify(){
        $Vpay=new Vpay();
        if($Vpay->PayNotify($_GET)) {//异步回调验证通过
            //业务处理
            $json=json_decode(urldecode(arg("param")));
            //do something
            //可以自己通知用户
            log(urldecode(arg("param")));
            $mail=new Email();
            if(isset($json->email)&&Email::isEmail($json->email)){
                //通知用户
                $item=new Item();
                $result=$item->getOne($json->id);
                if($result){
                    $content=$this->replace($result['msg'],$_GET['payId'],intval($_GET['type'])===1?'支付宝':'微信',$_GET['price'],$_GET['reallyPrice']);

                    $mail->send($json->email,'支付结果通知',$content,'Vpay');
                }


            }
            //通知作者，id是商品ID
            $conf=new Config();
            $mailAddress=$conf->getData('MailRec');
            if(isset($json->id)&&($json->id==2||$json->id==3)&&Email::isEmail($mailAddress)){
                $content=<<<ROF
用户已经购买 {$json->name} 请尽快处理<br>
用户邮箱： {$json->email} <br>
用户留言： {$json->remark} 
ROF;
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
        $item=new Item();
        $goodlist=$item->getOne(arg('payGood'));


        if(!$goodlist)$this->tips('没有该商品！',url('main','index'));

        $price=$goodlist['price'];//价格
        $name=$goodlist['name'];//商品名称

        $email=arg('email');
        if(!Email::isEmail($email))$this->tips('邮箱输入错误！',url('main','index'));
        $remark=arg('remark');

        $param=urlencode(json_encode(array('name'=>$name,'email'=>$email,'remark'=>$remark,'id'=>arg('payGood'))));
        //附加参数为文本型
        $vpay=new Vpay();

        $payId=$vpay->getPayId($price,$param);

        $type=intval(arg('payType'))===1?1:2;

        $html=1;//是否使用自带的支付页面，为0表示不使用自带的支付页面

        $arg["payId"]=$payId;
        $arg["price"]=$price;
        $arg["explain"]='商品 '.$name;//可选
        $arg["param"]=$param;
        $arg["type"]=$type;

        $result=$vpay->Create($arg,$html);
        if($result===false) $this->tips($vpay->getErr(),url('main','index'));
        else echo $result;

    }

    private function replace($msg,$payId,$type,$price,$reallyPrice){
        $msg=str_replace(array(
            "{payId}","{type}","{price}","{reallyPrice}"
        ),array(
            $payId,$type,$price,$reallyPrice,
        ),$msg);
        return $msg;
}


}