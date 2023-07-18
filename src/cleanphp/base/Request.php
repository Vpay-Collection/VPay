<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\base;


/**
 * Class Request
 * @package cleanphp\web
 * Date: 2020/11/22 11:18 下午
 * Author: ankio
 * Description:客户端请求处理
 */
class Request
{
    /**
     * 获取header头部内容
     * @param $headName
     * @return mixed|null
     */
    public static function getHeaderValue($headName): mixed
    {
        $headers = self::getHeaders();
        if (isset($headers[$headName])) {
            return $headers[$headName];
        }
        return null;
    }

    /**
     * 获取头部信息
     * @return array|false
     */
    public static function getHeaders(): bool|array
    {

        if (function_exists('getallheaders')) return getallheaders();
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[ucfirst(strtolower(str_replace('_', '-', substr($key, 5))))] = $value;
            }
            if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
            } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $headers['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
            }
            if (isset($_SERVER['CONTENT_LENGTH'])) {
                $headers['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
            }
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $headers['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
            }
        }
        return $headers;
    }



    /**
     * 获取客户端真实IP
     * @return string
     */
    public static function getClientIP(): string
    {
        return $_SERVER["REMOTE_ADDR"];
    }


    /**
     * 是否PJAX请求
     * @return bool
     */
    public static function isPjax(): bool
    {
        return (isset($_SERVER['HTTP_X_PJAX']) && $_SERVER['HTTP_X_PJAX'] == 'true');
    }


    /**
     * 是否AJAX请求
     * @return bool
     */
    public static function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }


    /**
     * 是否GET请求
     * @return bool
     */
    public static function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    public static function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否POST请求
     * @return bool
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    /**
     * 获取当前访问的URL域名
     * 例如：https://example.com
     * @return string
     */
    public static function getAddress(): string
    {

        return Response::getHttpScheme() . $_SERVER["HTTP_HOST"];
    }

    /**
     * 获取根域名
     * 例如hot.baidu.com,获取到是baidu.com
     * @return string
     */
    public static function getRootDomain(): string
    {
        $url = Response::getHttpScheme() . $_SERVER ['HTTP_HOST'];
        $hosts = parse_url($url);
        $host = $hosts['host'];
        //查看是几级域名
        $data = explode('.', $host);
        $n = count($data);
        //判断是否是双后缀
        $preg = '/\w.+\.(com|net|org|gov|edu)\.cn$/';
        if (($n > 2) && preg_match($preg, $host)) {
            //双后缀取后3位
            $host = $data[$n - 3] . '.' . $data[$n - 2] . '.' . $data[$n - 1];
        } else {
            //非双后缀取后两位
            $host = $data[$n - 2] . '.' . $data[$n - 1];
        }
        return $host;
    }

    /**
     * 获取域名，无端口
     * 例如：example.com
     * @return string
     */
    public static function getDomainNoPort(): string
    {
        return $_SERVER["SERVER_NAME"];
    }

    /**
     * 获取域名
     * 例如：example.com 或 example.com:8088
     * @return string
     */
    public static function getDomain(): string
    {
        return $_SERVER["HTTP_HOST"];
    }

    /**
     * 获取当前访问的地址
     * 例如：https://example.com/index/main
     * @return string
     */
    public static function getNowAddress(): string
    {
        return Response::getHttpScheme() . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取当前服务器IP
     * 例如：127.0.0.1
     */
    public static function getServerIp(): string
    {
        return gethostbyname(gethostname());
    }


    /**
     * 是否为内网IP
     * @param string $ip
     * @return bool 返回false表示不是内网ip
     */
    public static function isInnerIp(string $ip): bool
    {
        return !(preg_match('%^127\.|10\.|192\.168|172\.(1[6-9]|2|3[01])%', $ip) === 0);
    }


}
