<?php


namespace app\lib\URL;

/**
 * Class DefenseAgainstSSRF
 * @package Security\URLSecurity
 */
class DefenseAgainstSSRF
{

    /**
     * @var int
     */
    private int $timeout;

    /**
     * @var int
     */
    private int $limit;
    private string $err="";

    /**
     * DefenseAgainstSSRF constructor.
     */
    function __construct()
    {
        //默认2s超时
        $this->timeout = 2;
        //默认跳转2次
        $this->limit = 2;
    }


    /**
     * 设置超时时间
     * @param $var
     */
    public function setTimeout($var)
    {
        $this->timeout = $var;
    }


    /**
     * 设置跳转等待时间
     * @param $var
     */
    public function setJmpLimit($var)
    {
        $this->limit = $var;
    }


    /**
     * 获取错误
     * @return string
     */
    public function getErr(): string
    {
        return $this->err;
    }

    /**
     * 验证ssrf
     * @param $url
     * @return bool
     */
    public function verifySSRFURL($url): bool
    {


        if (!$this->checkDomain($url)) {
            $this->err.="(域名校验未通过)";
            return false;
        }

        $ip = $this->getRealIP($url);
        if (!$ip) {
            $this->err.="(IP校验未通过)";
            return false;
        }

        if ($this->isInnerIP($ip)) {
            $this->err.="内网IP校验未通过";
            return false;
        }
        return true;
    }


    /**
     * 检测域名
     * @param $url
     * @return bool
     */
    private function checkDomain($url): bool
    {
        $this->err= "非文本类型";
        if (!is_string($url)) {
            return false;
        }
        $host = parse_url($url);
        $this->err="解析URL失败";
        if (!isset($host) || !isset($host['scheme'])) {
            return false;
        }
        $this->err="不是http或https协议";
        if (!in_array($host['scheme'], array('http', 'https'))) {
            return false;
        }
        $this->err="存在需要认证的页面";
        if (isset($host["user"]) || isset($host["pass"])) {
            return false;
        }
        $this->err="没有主机名";
        if (!isset($host["host"])) {
            return false;
        }
        $this->err="";
        return true;
    }


    /**
     * 获取真实IP
     * @param $url
     * @return bool|string
     */
    private function getRealIP($url)
    {
        $count = 0;
        $info = $this->getURLInfo($url);
        $this->err="url信息 ".print_r($info,true);
        while ($count < $this->limit - 1 && $info['status'] >= 300 && $info['status'] < 400) {
            $count++;
            $info = $this->getURLInfo($info['location']);
        }
        $this->err="大于 {$this->limit} 次跳转 或 最后一次请求出错 ".print_r($info,true);
        //大于$limit 次跳转 或 最后一次请求出错
        if ($info['status'] >= 300 || $info['status'] < 200) {
            return false;
        }
        //$this->err="大于 n 次跳转 或 最后一次请求出错";
        if (!$this->checkDomain($info['host'])) {
            return false;
        }

        $host = @parse_url($info['host'])['host'];
        $ip = gethostbyname($host);
        return $ip;
    }


    /**
     * 获取URL信息
     * @param $url
     * @return array
     */
    private function getURLInfo($url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ret = array();
        $match = array();
        $ret['status'] = intval($status);

        if ($ret['status'] >= 300 && $ret['status'] < 400) {
            preg_match("#location: ([^\s]*)#i", $result, $match);
            if (substr($match[1], 0, 4) === 'http') {
                $ret['location'] = $match[1];
            } else {
                $ret['location'] = $url . $match[1];
            }
        }

        if ($ret['status'] == 200) {
            $ret['host'] = $url;
        }
        curl_close($ch);
        return $ret;
    }


    /**
     * 判断是否为内网IP
     * @param $ip_arg
     * @return bool
     */
    private function isInnerIP($ip_arg): bool
    {
        $ip = ip2long($ip_arg);
        return ip2long('127.0.0.0') >> 24 === $ip >> 24 or \
                ip2long('10.0.0.0') >> 24 === $ip >> 24 or \
                ip2long('172.16.0.0') >> 20 === $ip >> 20 or \
                ip2long('192.168.0.0') >> 16 === $ip >> 16;
    }
}
