<?php
namespace app\model;
use app\includes\AES;
use app\lib\speed\mvc\Model;
/*系统设置模块
 * */

class Config extends Model
{
    //查找的常量定义
    const UserName = 'UserName';//用户名
    const UserPassword = 'UserPassword';//登录密码
    const Key = 'Key';//通讯密钥
    const LastHeart = 'LastHeart';//监控端上一次的心跳时间
    const LastPay = 'LastPay';//上次支付的时间
    const State = 'State';//监控端状态
    const ValidityTime = 'ValidityTime';//订单有效期
    const Payof='Payof';//出现多个重复金额的订单按递增还是递减来区分
    const WechatPay = 'WechatPay';//微信收款码
    const AliPay = 'AliPay';//支付宝收款码
    const Ailuid = 'Ailuid';//支付宝UID
    const LastLogin = 'LastLogin';//最后登录时间
    const Shop = 'UseShop';//使用内置的商城
    //接口状态常量
    const Api_Ok=0;//接口状态ok
    const Api_Err=-1;//接口状态错误
    //监控端常量
    const State_Online=1;//监控在线
    const State_Offline=0;//监控掉线
    const State_Nobind=-1;//监控还没绑定
    //递增递减
    const PayIncrease=1;//递增
    const PayReduce=2;//递减

    public function __construct()
    {
        parent::__construct('pay_settings');
    }

    public function setDataAll($config)
    {
        if ($config["UserPassword"] === "") {
            $config["UserPassword"] = $this->getData(self::UserPassword);
        }else{
            $aes=new AES();
            $config["UserPassword"]=$aes->decrypt($config["UserPassword"],$_SESSION['key']);
            $config["UserPassword"] = hash("sha256",md5($config["UserPassword"].md5($config["UserName"])));
        }
        foreach ($config as $index => $value) {
            $this->insertDuplicate(array("vkey" => $index,"vvalue" => $value),array("vvalue"));
            //$this->update(array("vkey" => $index), array("vvalue" => $value));
        }
        return json_encode(array("code" => self::Api_Ok, "msg" => "保存成功!"));
    }

    public function getData($id)
    {
        $query = $this->select(array("vkey" => $id));
        if ($query) return $query["vvalue"];
        else return false;
    }

    public function setData($id, $v)
    {

        if ($id === "UserPassword") $v = hash("sha256",md5($v.md5($this->getData(self::UserName))));//前端使用md5加盐，密码更新使用sha256加盐
        $this->insertDuplicate(array("vkey" => $id,"vvalue" => $v),array("vvalue"));
    }
}