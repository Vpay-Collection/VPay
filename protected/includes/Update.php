<?php
namespace app\includes;


/**
 * Class Update
 * @package includes
 * @description 用于检查github是否有更新
 */
class Update{
    private $updateUrl="";
    private $reason="";
    private $needUpdate=false;
    private $v0="1.0";
    private $v1="1.0";
    public function __construct($version){
        $url="https://api.github.com/repos/dreamncn/VPay/releases/latest";
        $web=new Web();
        $result=$web->get($url,"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko, By Black Prism) Chrome/99.0 Safari/537.36");

        $result=json_decode($result,true);

        $lastest=$result['tag_name'];
        $this->reason=$result['body'];
        $this->updateUrl=$result['zipball_url'];
        $this->v0=$version;
        $this->v1=$lastest;
        if(floatval($lastest)>floatval($version))$this->needUpdate=true;
    }
    public function getReason(){
        return $this->reason;
    }
    public function getUrl(){
        return $this->updateUrl;
    }
    public function boolUpdate(){
        return $this->needUpdate;
    }

    public function getLastest()
    {
        return $this->v1;
    }

    public function getVer()
    {
        return $this->v0;
    }
}