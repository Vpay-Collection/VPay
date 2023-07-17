<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\engine
 * Class ResponseEngine
 * Created By ankio.
 * Date : 2022/11/11
 * Time : 17:30
 * Description :
 */

namespace cleanphp\engine;

use cleanphp\base\Response;

abstract class BaseEngine
{




    /**
     * 渲染数据
     * @param $data
     * @return string
     */
    abstract function render(...$data): string;

    /**
     * 错误渲染
     * @param string $msg 错误信息
     * @param array $traces 堆栈
     * @param string $dumps 错误发生之前的输出信息
     */
    abstract function renderError(string $msg, array $traces, string $dumps, string $tag);

    private array $headers = [];

    /**
     * 设置请求头
     * @param $k
     * @param $v
     * @return $this
     */
    public function setHeader($k,$v): static
    {
        $this->headers[$k] = $v;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 设置缓存
     * @param $min
     * @return $this
     */
    public function cache($min): static
    {
        $seconds_to_cache = $min * 60 ;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        $this->headers["Expires"] = $ts;
        $this->headers["Pragma"] = "cache";
        $this->headers["Cache-Control"] = "max-age=$seconds_to_cache";
        return $this;
    }

    private string $content_type = 'text/html';
    //状态
    private int $code = 200;
    /**
     * 设置响应类型
     * @param $type
     * @return $this
     */
    public function setContentType($type): static
    {
        $this->content_type = $type;
        return $this;
    }

    /**
     * 渲染的输出类型
     * @return string
     */
    function getContentType(): string{
        return $this->content_type;
    }

    /**
     * 设置响应类型
     * @param $code
     * @return $this
     */
    public function setCode($code): static
    {
        $this->code = $code;
        return $this;
    }

    function getCode(): int
    {
        return $this->code;
  }

    /**
     * 渲染数据
     */
    function renderMsg(int $code = 404, string $title = "", $msg = "", int $time = -1, string $url = '/', string $desc = "立即跳转"): string
    {
        $this->setCode($code);
        if ($time == 0) {
            Response::location($url);
        }
        return "";
    }

    /**
     * 控制器渲染自定义错误
     * @param $controller
     * @param $method
     * @return string|null
     */
    function onControllerError($controller, $method): ?string
    {
        return null;
    }

    abstract function onNotFound($msg = "");

}