<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * File Response.php
 * Author : Ankio
 * Date : 7/30/2020 12:49 AM
 * Description:响应类
 */

namespace cleanphp\base;


use cleanphp\App;

/**
 * Class Response
 * @package cleanphp\web
 * Date: 2020/11/22 11:21 下午
 * Author: ankio
 * Description:客户端响应类
 */
class Response
{
    // 原始数据
    protected string $data;
    //状态
    protected int $code = 200;
    // header参数
    protected array $header = [];

    /**
     * 直接跳转
     * @param string $url 跳转路径
     * @param int $timeout 延时跳转
     */
    public static function location(string $url, int $timeout = 0): void
    {
        if (!str_starts_with($url,"http")) {
            $url = Response::getHttpScheme() . Request::getDomain() . $url;
        }
        if ($timeout !== 0) {
            header("refresh:$timeout," . $url);
        } else {
            http_response_code(302);
            header("Location:{$url}");
        }
        App::exit(sprintf("发生强制跳转：%s", $url));
    }

    /**
     * 获取浏览器的http协议
     * @return string
     */
    static function getHttpScheme(): string
    {
        if (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == "https"
            || isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"
            || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443
        ) {
            return 'https://';
        } else {
            return 'http://';
        }
    }


    /**
     * 原始数据
     * @access   public
     * @param string $data 输出数据
     * @return Response
     */
    public function render(string $data = ''): Response
    {

        $this->data = $data;//需要渲染的数据
        return $this;
    }

    /**
     * 发送的数据类型
     * @param string $content_type 响应类型
     * @param string $charset 编码
     * @return $this
     */
    public function contentType(string $content_type = 'text/html', string $charset = 'utf-8'): Response
    {
        $this->header['Content-Type'] = $content_type . '; charset=' . $charset;
        return $this;
    }

    /**
     * 发送HTTP状态
     * @param integer $code 状态码
     */
    public function code(int $code): Response
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 设置缓存分钟数
     * @param $min
     * @return $this
     */
    public function cache($min): Response
    {
        $seconds_to_cache = $min * 60 ;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        $this->header["Expires"] = $ts;
        $this->header["Pragma"] = "cache";
        $this->header["Cache-Control"] = "max-age=$seconds_to_cache";
        return $this;
    }

    /**
     * 设置header头
     * @param $key
     * @param $value
     * @return $this
     */
    public function header($key,$value): Response
    {
        $this->header[$key] = $value;
        return $this;
    }

    public function setHeaders($header = []): static
    {
        $this->header = $header;
        return $this;
    }

    public function send(): void
    {
        //允许跨域
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (in_array(str_replace(self::getHttpScheme(), '', $origin), Config::getConfig("frame")['host'])) {
            $this->header['Access-Control-Allow-Origin'] = $origin;
        }

        $addr = Request::getNowAddress();
        $addr = strstr($addr, '?', true) ?: $addr;
        if (preg_match("/.*\.(gif|jpg|jpeg|png|bmp|swf|woff|woff2)?$/", $addr)) {
            $this->cache(60 * 24 * 365);
        } elseif (preg_match("/.*\.(js|css)?$/", $addr)) {
            $this->cache(60 * 24 * 180);
        }

        if(App::$debug){
            $this->header[] = "Server-Timing: ".
            "Total;dur=".round((microtime(true) - Variables::get("__frame_start__", 0)) * 1000,4).
            ",Route;dur=".App::$route.
                ",Frame;dur=".App::$frame .
                ",App;dur=".App::$route .
                ",Db;dur=".App::$db;
        }

        $this->header["Server"] = "Apache";
        // 监听response_send
        EventManager::trigger('__response_before_send__', $this);

        // 处理输出数据
        if (!headers_sent() && !empty($this->header)) {
            // 发送状态码
            http_response_code($this->code);
            // 发送头部信息
            foreach ($this->header as $name => $val) {
                if (!is_string($name)) {
                    header($val);
                } else {
                    header($name . ':' . $val);
                }
            }
        }
        if(is_file_exists($this->data)){
            readfile($this->data);
        }else{
            echo $this->data;
        }

        self::finish();

        // 监听response_end
        EventManager::trigger('__response_after_send__', $this);

        App::exit("后端数据发送结束");
    }

    /**
     * 结束Http响应
     * @return void
     */
    static function finish(): void
    {
        if (function_exists('fastcgi_finish_request')) {
            // 提高页面响应
            fastcgi_finish_request();
        }
        flush();
    }
}
