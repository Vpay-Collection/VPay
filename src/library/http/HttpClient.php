<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\http;


use cleanphp\App;
use cleanphp\file\Log;
use Error;

/**
 * Package: library\http
 * Class HttpClient
 * Created By ankio.
 * Date : 2022/11/20
 * Time : 19:55
 * Description : http客户端
 */
class HttpClient
{
    private $curl = null;
    private string $base_url = "";
    private string $path = "";
    private string $url_params = "";
    private array $headers = [];
    private string $cookie = "";
    private string $cookie_key = "";

    public function __construct($base_url)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $this->headers['user-agent'] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.54";
        $this->base_url = $base_url;
    }

    /**
     * 初始化
     * @param $base_url string 基础URL
     * @return HttpClient
     */
    static function init(string $base_url): HttpClient
    {
        return new HttpClient($base_url);
    }

    public function __destruct()
    {

        curl_close($this->curl);
    }

    public function setHeaders($headers = []): HttpClient
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * get请求
     * @return $this
     */
    function get(): HttpClient
    {
        return $this->setOption(CURLOPT_HTTPGET, true);
    }

    /**
     * 设置CURL选项
     * @param int $curl_opt
     * @param mixed $value
     * @return HttpClient
     */
    public function setOption(int $curl_opt, $value): HttpClient
    {
        curl_setopt($this->curl, $curl_opt, $value);
        return $this;
    }

    /**
     * post请求
     * @param array|string $data post的数据
     * @param string $content_type
     * @return $this
     */
    public function post($data, string $content_type = 'json'): self
    {
        $this->setOption(CURLOPT_POST, true);
        $this->setData($data, $content_type);
        return $this;
    }

    private function setData($data, string $content_type = 'json')
    {
        $this->headers["Content-Type"] = $content_type;
        if ($content_type == 'form') {
            $this->headers["Content-Type"] = 'application/x-www-form-urlencoded';
            if (is_array($data)) {
                $data = http_build_query($data);
            }
        } elseif ($content_type == 'json') {
            $this->headers["Content-Type"] = 'application/json';
            $data = json_encode($data);
        }
        //$this->headers["content-length"] = mb_strlen($data);
        $this->setOption(CURLOPT_POSTFIELDS, $data);
    }

    /**
     * put请求
     * @param array $data
     * @param string $content_type
     * @return $this
     */
    function put(array $data, string $content_type = 'json'): HttpClient
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, "PUT");
        $this->setData($data, $content_type);
        return $this;
    }

    /**
     * patch请求
     * @param array $data
     * @param string $content_type
     * @return $this
     */
    function patch(array $data, string $content_type = 'json'): HttpClient
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, "PATCH");
        $this->setData($data, $content_type);
        return $this;
    }

    /**
     * delete请求
     * @return $this
     */
    function delete(): HttpClient
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this;
    }

    function mergeCookies($wait)
    {
        parse_str(str_replace(';', '&', urldecode($this->cookie)), $cookie_array);
        return str_replace('&', '; ', http_build_query(array_merge($cookie_array, $wait)));
    }

    function autoUpdateCookie(&$cookie): HttpClient
    {
        $this->cookie = &$cookie;
        return $this;
    }

    /**
     * 发出请求
     * @param string $path
     * @param array $url_params
     * @return HttpResponse
     * @throws HttpException
     */
    public function send(string $path = '/', array $url_params = []): ?HttpResponse
    {
        $this->path = $path;
        if (count($url_params)) {
            $this->url_params = http_build_query($url_params);
        }

        $this->setOption(CURLOPT_URL, $this->url());

        $headers = [];
        foreach ($this->headers as $key => $header) {
            if (!is_int($key)) {
                $headers[] = "$key: $header";
            } else {
                $headers[] = $header;
            }
        }

        $this->setOption(CURLOPT_HTTPHEADER, $headers);
        $this->setOption(CURLOPT_RETURNTRANSFER, true);

        try {
            if (App::$debug) {
                $this->setOption(CURLOPT_VERBOSE, true);
                $streamVerboseHandle = fopen('php://temp', 'w+');
                $this->setOption(CURLOPT_STDERR, $streamVerboseHandle);
            }

            $request_exec = curl_exec($this->curl);


            if ($request_exec === false) {
                throw new HttpException("HttpClient Error: " . curl_errno($this->curl) . " " . curl_error($this->curl));
            }

            $header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
            $header = substr($request_exec, 0, $header_size);


            if (App::$debug && isset($streamVerboseHandle)) {
                rewind($streamVerboseHandle);
                $verboseLog = stream_get_contents($streamVerboseHandle);
                Log::record('HttpClient Result', "┌---------------------HTTP CURL DEBUG---------------------");
                Log::record('HttpClient Result', "│");
                Log::recordAsLine('HttpClient Result', $verboseLog, Log::TYPE_INFO, "│ ");
                Log::record('HttpClient Result', "│");
                Log::record('HttpClient Result', "│" . $request_exec);
                Log::record('HttpClient Result', "└------------------------------------------------------");
            }

            preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
            if (!empty($this->cookie)) {
                $cookies = [];
                foreach ($matches[1] as $item) {
                    parse_str($item, $cookie);
                    $cookies = array_merge($cookies, $cookie);
                }
                $this->cookie = $this->mergeCookies($cookies);
            }

            return new HttpResponse($this->curl, $request_exec);

        } catch (Error $e) {
            throw new HttpException($e->getMessage());
        }

    }

    /**
     * 构造url
     * @return string
     */
    private function url(): string
    {
        $base = rtrim($this->base_url, '/');
        $path = ltrim($this->path, '/');
        $url = $base . "/" . $path;

        if ($this->url_params != '') {
            $url .= "?{$this->url_params}";
        }

        return $url;
    }

    /**
     * 接受GZIP
     * @return $this
     */
    public function gzip(): HttpClient
    {
        $this->setOption(CURLOPT_ENCODING, "gzip");
        return $this;
    }
}