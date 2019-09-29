<?php

/*系统设置模块
 * */

class Config extends Model
{
    //查找的常量定义
    const UserName = 1;//用户名
    const UserPassword = 2;//登录密码
    const Install = 3;//是否已经安装
    const Key = 5;//通讯密钥
    const LastHeart = 6;//监控端上一次的心跳时间
    const LastPay = 7;//上次支付的时间
    const State = 8;//监控端状态
    const ValidityTime = 9;//订单有效期
    const Payof=10;//出现多个重复金额的订单按递增还是递减来区分
    const WechatPay = 11;//微信收款码
    const AliPay = 12;//支付宝收款码
    const Ailuid = 13;//支付宝UID
    const LastLogin = 15;//最后登录时间
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

    public function __construct($table_name = "pay_settings")
    {
        parent::__construct($table_name);
    }

    public function UpdateDataAll($config)
    {
        if ($config["UserPassword"] === "") {
            $config["UserPassword"] = $this->GetData(self::UserPassword);
        }else{
            $des=new Des();
            $config["UserPassword"]=$des->encrypt($config["UserPassword"],$config["_t"]);
            $config["UserPassword"] = hash("sha256",$config["UserPassword"].$config["UserName"]);
        }
        foreach ($config as $index => $value) {
            $this->update(array("vkey" => $index), array("vvalue" => $value));
        }
        return json_encode(array("code" => self::Api_Ok, "msg" => "保存成功!"));
    }

    public function GetData($id)
    {
        //具体要查询的数据
        switch ($id) {
            case self::UserName:
                $query = $this->find(array("vkey" => "UserName"));
                break;
            case self::UserPassword:
                $query = $this->find(array("vkey" => "UserPassword"));
                break;
            case self::Install:
                $query = $this->find(array("vkey" => "Install"));
                break;
            case self::Key:
                $query = $this->find(array("vkey" => "Key"));
                break;
            case self::LastHeart:
                $query = $this->find(array("vkey" => "LastHeart"));
                break;
            case self::LastPay:
                $query = $this->find(array("vkey" => "LastPay"));
                break;
            case self::State:
                $query = $this->find(array("vkey" => "LastHeart"));
                if ($query)
                {
                    if($query["vvalue"]==="")return self::State_Nobind;
                    if ((time() - intval($query["vvalue"])) > 120)return self::State_Offline;
                    else return self::State_Online;

                }
                else return self::State_Nobind;
                break;
            case self::ValidityTime:
                $query = $this->find(array("vkey" => "ValidityTime"));
                break;
            case self::WechatPay:
                $query = $this->find(array("vkey" => "WechatPay"));
                break;
            case self::AliPay:
                $query = $this->find(array("vkey" => "AliPay"));
                break;
            case self::Ailuid:
                $query = $this->find(array("vkey" => "Ailuid"));
                break;
            case self::LastLogin:
                $query = $this->find(array("vkey" => "LastLogin"));
                break;
            case self::Payof:
                $query = $this->find(array("vkey" => "Payof"));
                break;
        }
        if ($query) return $query["vvalue"];
        else return false;
    }

    public function UpdateData($id, $v)
    {
        if ($id === "UserPassword") $v = hash("sha256",$v.$this->GetData(self::UserName));//前端使用md5加盐，密码更新使用sha256加盐
        $this->update(array("vkey" => $id), array("vvalue" => $v));
    }
}