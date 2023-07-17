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

use cleanphp\App as CleanApp;
use cleanphp\file\Log;

class HttpResponse
{
    protected array $headers;
    protected string $body = '';
    protected int $http_code;
    protected array $meta;
    private string $cookie = "";

    public function __construct($curl, $request_headers, $request_exec)
    {
        $this->meta = curl_getinfo($curl);
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->meta['execution_time'] = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        CleanApp::$debug && Log::record("HttpClient", "请求时间：" . $this->meta['execution_time'] . "秒", Log::TYPE_WARNING);
        $this->setBody($curl, $request_headers, $request_exec);
    }

    public function getCookie(): string
    {
        return $this->cookie;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHttpCode(): int
    {
        return $this->http_code;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    protected function setBody($client, $request_headers, $request_exec): void
    {
        $header_len = curl_getinfo($client, CURLINFO_HEADER_SIZE);
        $header_string = substr($request_exec, 0, $header_len);
        $this->setHeaders($request_headers, $header_string);
        $this->body = substr($request_exec, $header_len);

        if (CleanApp::$debug) {
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

    protected function setHeaders(array $request, string $header_string): void
    {
        $headers_indexed_arr = explode("\r\n", $header_string);
        $headers_arr = [];

        foreach ($headers_indexed_arr as $value) {
            $matches = explode(':', $value, 2);
            if (isset($matches[0]) && isset($matches[1])) {
                $headers_arr[$matches[0]] = trim($matches[1]);
            }
        }

        $this->headers = $headers_arr;

        if(!preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header_string, $matches)){
            return;
        }

        $cookie = '';
        foreach ($request as $value) {
            if (strtolower(substr($value, 0, 6)) == 'cookie') {
                $cookie = substr($value, 8);
                break;
            }
        }

        $cookies = [];
        parse_str(str_replace(';', '&', $cookie), $cookies);

        $cookieArr = [];
        foreach ($matches[1] as $cookieString) {
            parse_str($cookieString, $_cookie);
            $cookies = array_merge($cookies, $_cookie);
        }

        foreach ($cookies as $name => $value) {
            $cookieArr[] = $name . '=' . $value;
        }

        $this->cookie = implode('; ', $cookieArr);
    }
}
