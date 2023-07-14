<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\http
 * Class HttpResponse
 * Created By ankio.
 * Date : 2022/11/20
 * Time : 21:27
 * Description :
 */

namespace library\http;

use cleanphp\App;
use cleanphp\file\Log;

class HttpResponse
{
    protected array $headers;
    protected string $body = '';
    protected int $http_code;
    protected array $meta;
    /**
     * @var array|string|string[]
     */
    private string $cookie = "";

    public function __construct($curl,$request_headers, $request_exec)
    {
        $this->meta = curl_getinfo($curl);
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->meta['execution_time'] = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        App::$debug && Log::record("HttpClient", "请求时间：" . $this->meta['execution_time'] . "秒", Log::TYPE_WARNING);
        $this->setBody($curl,$request_headers, $request_exec);
    }

    /**
     * 设置body
     * @param $client
     * @param $request_exec
     * @return void
     */
    private function setBody($client,$request_headers, $request_exec)
    {
        $header_len = curl_getinfo($client, CURLINFO_HEADER_SIZE);
        $header_string = substr($request_exec, 0, $header_len);
        $this->setHeaders($request_headers,$header_string);
        $this->body = substr($request_exec, $header_len);





        if (App::$debug) {
            Log::record('HttpClient Result', "┌---------------------HTTP RESPONSE---------------------");
            Log::record('HttpClient Result', "│");
            Log::record('HttpClient Result', "│");
            Log::record('HttpClient Result', "│" . $this->http_code);
            Log::record('HttpClient Result', "│" . curl_getinfo($client, CURLINFO_EFFECTIVE_URL));
            Log::record('HttpClient Result', "│");
            Log::record('HttpClient Result', "│");
            Log::record('HttpClient Result', "│" . $this->body);
            Log::record('HttpClient Result', "└------------------------------------------------------");
        }

    }

    /**
     * 获取Cookie
     * @return string
     */
    public function getCookie():string{
        return $this->cookie;
    }

    /**
     * 获取响应数据
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * 获取响应代码
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->http_code;
    }

    /**
     * 获取响应头
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $header_string
     * @return void
     */
    protected function setHeaders(array $request,string $header_string)
    {
        $cookie = "";
        foreach ($request as $value){
            if(strtolower(substr($value,0,6))=="cookie"){
                $cookie = substr($value,8);
            }
        }


        // Convert the $headers string to an indexed array
        $headers_indexed_arr = explode("\r\n", $header_string);

        // Define as array before using in loop
        $headers_arr = array();
        // Remember the status message in a separate variable
        $status_message = array_shift($headers_indexed_arr);

        // Create an associative array containing the response headers
        foreach ($headers_indexed_arr as $value) {
            if (false !== ($matches = explode(':', $value, 2))) {
                if (isset($matches[0]) && isset($matches[1])) {
                    $headers_arr["{$matches[0]}"] = trim($matches[1]);
                }
            }
        }

        $this->headers = $headers_arr;

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header_string, $matches);

        if (!empty($cookie)) {
            parse_str(str_replace(';','&',$cookie), $cookies);
        }else{
            $cookies = [];
        }

        foreach ($matches[1] as $item) {
            parse_str($item, $_cookie);
            $cookies = array_merge($cookies, $_cookie);
        }

        $this->cookie = str_replace('&', '; ', http_build_query($cookies));

    }


}