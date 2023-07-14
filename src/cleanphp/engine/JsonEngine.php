<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\engine
 * Class JsonEngine
 * Created By ankio.
 * Date : 2022/11/11
 * Time : 17:58
 * Description :
 */

namespace cleanphp\engine;


use cleanphp\base\EventManager;
use cleanphp\base\Json;
use cleanphp\base\Response;


class JsonEngine extends BaseEngine
{
    private array $tpl;

    /**
     * 初始化参数，每一个参数名字
     * @param array $template
     */
    public function __construct(array $template = ["code" => 0, "msg" => "OK", "data" => null, "count" => 0])
    {
        $this->tpl = $template;
    }

    function getContentType(): string
    {
        return "application/json";
    }


    /**
     * json数据渲染
     * @param ...$data
     * @return string
     */
    function render(...$data): string
    {
        $array = [];
        $i = 0;
        foreach ($this->tpl as $key => $value) {
            if (isset($data[$i])) {
                $array[$key] = $data[$i];
            } else {
                $array[$key] = $value;
            }
            $i++;
        }
        return Json::encode($array);
    }

    /**
     * @param string $msg
     * @param array $traces
     * @param string $dumps
     * @param string $tag
     * @return string
     */
    function renderError(string $msg, array $traces, string $dumps, string $tag): string
    {


        $ret = [
            "error" => true,
            "msg" => $msg,
            "traces" => $traces,
            "dumps" => $dumps
        ];
        EventManager::trigger("__json_render_error__", $ret, true);
        return JSON::encode($ret);
    }

    public function renderMsg(bool $err = false, int $code = 404, string $title = "", $msg = "", int $time = 3, string $url = '/', string $desc = "立即跳转"): string
    {
        $array = [
            "code" => $code, "msg" => $title, "data" => $msg, 'url' => $url
        ];
        EventManager::trigger("__json_render_msg__", $array, true);
        return Json::encode($array);
    }



    public function onNotFound($msg = ""): void
    {
        (new Response())->code(404)
            ->contentType($this->getContentType())
            ->render($this->renderMsg(true, 404, "404 not found", "404 not found"))
            ->send();
    }


}