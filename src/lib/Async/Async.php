<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\lib\Async;
use app\core\cache\Cache;
use app\core\utils\StringUtil;
use app\core\web\Request;
use app\core\web\Response;

/**
 * Class Async
 * @package app\extend\net_ankio_tasker
 * Date: 2020/12/20 23:04
 * Author: ankio
 * Description:异步处理，多用于后台与多线程
 */
class Async
{
    private string $err = '';
    private static ?Async $instance=null;

    public function err(): string
    {
        return $this->err;
    }


    public function __construct()
    {
        Cache::init(300,APP_CACHE."task/");
    }

    /**
     * @return Async
     */
    public static function getInstance(): ?Async
    {
        return self::$instance===null?(self::$instance=new Async()):self::$instance;
    }
    /**
     *  发起异步请求，就是后台服务请求
     * @param string $url 完整的URL
     * @param string $method 调用的方法
     * @param array $data 传递的数据
     * @param array $cookie cookie数组
     * @param string $identify 唯一标识符
     * @return bool
     */
    public function request(string $url, string $method = 'GET', array $data = [], array $cookie = [], string $identify = 'clean'): bool
    {


        $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER

        if($data==[]&&isset($url_array["query"]))
            parse_str($url_array["query"], $data);

        $port = $url_array['scheme'] == 'http' ? 80 : 443;
        if(isset($url_array["port"])){
            $port = $url_array["port"];
        }
       try{
           $fp = @fsockopen(($url_array['scheme'] == 'http' ? "" : 'ssl://') . $url_array['host'], $port, $errno, $errstr, 30);
       }catch (\Exception $e){
           $this->err = '无法向该URL发起请求' . $errstr;
           return false;
       }
        if (!$fp) {
            $this->err = '无法向该URL发起请求' . $errstr;
            return false;
        }

       if ($method == 'GET' && $data!==[])
            $getPath = $url_array['path'] . "?" . http_build_query($data);
        else
            $getPath = $url_array['path'];

        $header = $method . " " . $getPath;
        $header .= " HTTP/1.1" . PHP_EOL;
        $header .= "Host: " . $url_array['host'] ./*":".$port .*/ PHP_EOL; //HTTP 1.1 Host域不能省略
        $token = StringUtil::get()->getRandom(128);

        $identify=md5($token . $identify);

        Cache::set($identify,['token' => $token, 'timeout' => time() + 60]);

        $header .= "Token: " . md5($token) . PHP_EOL;
        $header .= "Identify: $identify" . PHP_EOL;
        $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Async/1.0.0.1 " . PHP_EOL;
        $header .= "Connection:Close" . PHP_EOL;
        if (!empty($cookie)) {
            $_cookie = strval(null);
            foreach ($cookie as $k => $v) {
                $_cookie .= $k . "=" . $v . "; ";
            }
            $cookie_str = "Cookie: " . $_cookie . " " . PHP_EOL;//传递Cookie
            $header .= $cookie_str;
        }

        if (!empty($data)) {
            $_post = "" . PHP_EOL . http_build_query($data);
            $post_str = "Content-Type: application/x-www-form-urlencoded" . PHP_EOL;//POST数据
            $post_str .= "Content-Length: " . strlen($_post) . " " . PHP_EOL;//POST数据的长度
            $post_str .= $_post . PHP_EOL . PHP_EOL . " "; //传递POST数据

        }else{
            $post_str =   PHP_EOL . PHP_EOL . " "; //传递POST数据
        }
        $header .= $post_str;

        fwrite($fp, $header);
        fclose($fp);
        return true;
    }
    /**
     *  响应后台异步请求
     * @param int $time 最大运行时间
     * @return void
     */
    public function response(int $time = 0)
    {

        if (!$this->checkToken()) {
            Response::msg(true,403,"禁止访问","您无权访问该资源。",0,Response::getAddress(),"立即跳转");
        }
        ignore_user_abort(true); // 后台运行，不受前端断开连接影响
        set_time_limit($time);
        ob_end_clean();
        header("Connection: close");
        header("HTTP/1.1 200 OK");
        ob_start();
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();//输出当前缓冲
        flush();
        if (function_exists("fastcgi_finish_request")) {
            fastcgi_finish_request(); /* 响应完成, 关闭连接 */
        }
        sleep(1);
    }

    /**
     * 进行Token检查
     * @return bool
     */
    private function checkToken(): bool
    {
        $header = Request::getHeader();
        if (isset($header['Token']) && isset($header['Identify'])) {


            $data  =   Cache::get($header['Identify']);

            if (empty($data)) {
                $this->err = 'token缺失';
                return false;
            }

            Cache::del($header['Identify']);

            $token = $data;

            if ($token && isset($token['timeout']) && isset($token['token'])) {
                if (intval($token['timeout']) < time()) {
                    $this->err = '响应超时';
                    return false;
                }
                if ($header['Token'] !== md5($token['token'])) {
                    $this->err = 'token校验失败';
                    return false;
                }
                return true;
            }
        }
        $this->err = '任务不存在';
        return false;
    }
}
