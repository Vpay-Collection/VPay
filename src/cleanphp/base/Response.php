<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * File Response.php
 * Author : Ankio
 * Date : 7/30/2020 12:49 AM
 * Description:响应类
 */

namespace cleanphp\base;


use cleanphp\App;
use cleanphp\exception\ExitApp;
use cleanphp\objects\StringBuilder;

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
    // 当前的contentType
    protected string $content_type = 'text/html';
    //状态
    protected int $code = 200;
    // header参数
    protected array $header = [];

    /**
     * 直接跳转
     * @param string $url 跳转路径
     * @param int $timeout 延时跳转
     */
    public static function location(string $url, int $timeout = 0)
    {
        if (!(new StringBuilder($url))->startsWith("http")) {
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
     * @return mixed|string|null
     */
    static function getHttpScheme()
    {
        if (($http = Variables::get("__http_scheme__")) !== null) {
            return $http;
        }
        if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == "https") || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) {
            $http = 'https://';
        } else {
            $http = 'http://';
        }
        Variables::set('__http_scheme__', $http);
        return $http;
    }

    /**
     * 原始数据
     * @access   public
     * @param string $data 输出数据
     * @param int $code 响应代码
     * @param string $content_type 响应类型
     * @param array $header 响应头
     * @return Response
     */
    public function render(string $data = '', int $code = 200, string $content_type = 'text/html', array $header = []): Response
    {

        $this->data = $data;//需要渲染的数据
        $this->contentType($content_type);
        $this->header = array_merge($this->header, $header);
        $this->code = $code;
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
        if (empty($content_type)) $content_type = 'text/html';
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


    public function send()
    {
        //允许跨域
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (in_array(str_replace(self::getHttpScheme(), '', $origin), Config::getConfig("frame")['host'])) {
            $this->header['Access-Control-Allow-Origin'] = $origin;
        }

        $addr = Request::getNowAddress();
        $addr = strstr($addr, '?', true) ?: $addr;
        if (preg_match("/.*\.(gif|jpg|jpeg|png|bmp|swf|woff|woff2)?$/", $addr)) {
            $seconds_to_cache = 3600 * 24 * 365;//图片缓存30天
            $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
            $this->header["Expires"] = $ts;
            $this->header["Pragma"] = "cache";
            $this->header["Cache-Control"] = "max-age=$seconds_to_cache";
        } elseif (preg_match("/.*\.(js|css)?$/", $addr)) {
            $seconds_to_cache = 3600 * 24 * 180;//js和CSS缓存12小时
            $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
            $this->header["Expires"] = $ts;
            $this->header["Pragma"] = "cache";
            $this->header["Cache-Control"] = "max-age=$seconds_to_cache";
        }

        $this->header["Server"] = "Apache";

        // 监听response_send
        EventManager::trigger('__response_send__', $this);
        // 处理输出数据
        $data = $this->data;
        if (!headers_sent() && !empty($this->header)) {
            // 发送状态码
            http_response_code($this->code);
            // 发送头部信息
            foreach ($this->header as $name => $val) {
                if (is_null($val)) {
                    header($name);
                } else {
                    header($name . ':' . $val);
                }
            }
        }

        echo $data;

        self::finish();

        // 监听response_end
        EventManager::trigger('__response_end__', $this);

        App::exit("后端数据发送结束");
    }

    /**
     * 结束Http响应
     * @return void
     */
    static function finish()
    {
        if (function_exists('fastcgi_finish_request')) {
            // 提高页面响应
            fastcgi_finish_request();
        }
    }
}
