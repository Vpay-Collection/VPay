<?php
/*系统设置模块
 * */
class Config extends Model
{
    const UserName = 1;
    const UserPassword = 2;
    const Install = 3;
    const key = 5;
    const lastheart = 6;
    const lastpay = 7;
    const jkstate = 8;
    const close = 9;
    const payQf = 10;
    const wxpay = 11;
    const zfbpay = 12;
    const uid = 13;

    public function __construct($table_name = "setting")
    {
        parent::__construct($table_name);
    }

    public function UpdateDataAll($config)
    {
        if ($config["pass"] === "") {
            $config["pass"] = $this->GetData(self::UserPassword);
        }
        foreach ($config as $index => $value) {
            $this->update(array("vkey" => $index), array("vvalue" => $value));
        }
        return json_encode(array("code" => 1, "msg" => "保存成功"));
    }

    public function GetData($id)
    {
        //具体要查询的数据
        switch ($id) {
            case self::UserName:
                $query = $this->find(array("vkey" => "user"));
                break;
            case self::UserPassword:
                $query = $this->find(array("vkey" => "pass"));
                break;
            case self::Install:
                $query = $this->find(array("vkey" => "Install"));
                break;
            case self::key:
                $query = $this->find(array("vkey" => "key"));
                break;
            case self::lastheart:
                $query = $this->find(array("vkey" => "lastheart"));
                break;
            case self::lastpay:
                $query = $this->find(array("vkey" => "lastpay"));
                break;
            case self::jkstate:
                $query = $this->find(array("vkey" => "jkstate"));
                break;
            case self::close:
                $query = $this->find(array("vkey" => "close"));
                break;
            case self::payQf:
                $query = $this->find(array("vkey" => "payQf"));
                break;
            case self::wxpay:
                $query = $this->find(array("vkey" => "wxpay"));
                break;
            case self::zfbpay:
                $query = $this->find(array("vkey" => "zfbpay"));
                break;
            case self::uid:
                $query = $this->find(array("vkey" => "uid"));
                break;
        }
        if ($query) return $query["vvalue"];
        else return false;
    }

    public function UpdateData($id, $v)
    {
        if ($id === "pass") $v = md5($v);//密码更新使用md5
        $this->update(array("vkey" => $id), array("vvalue" => $v));
        //return json_encode(array("code"=>1,"msg"=>"保存成功"));
    }
}