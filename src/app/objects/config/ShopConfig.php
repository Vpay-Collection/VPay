<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\objects\config
 * Class ShopConfig
 * Created By ankio.
 * Date : 2023/7/20
 * Time : 12:56
 * Description :
 */

namespace app\objects\config;

use app\utils\ImageUpload;
use app\utils\XssFilter;
use cleanphp\base\Request;
use library\verity\VerityObject;
use library\verity\VerityRule;

class ShopConfig extends VerityObject
{
    public bool $state = false;
    public string $title = "Vpay";
    public string $image = "";
    public string $content = "";

    function onMerge(string $key, mixed $raw, mixed &$val): bool
    {
        if ($key === "content") {
            $val = (new XssFilter($val))->getHtml();
        }
        if ($key === "image" && $raw !== $val && !str_starts_with($raw, Request::getAddress())) {
            $image = new ImageUpload("shop");
            $val = $image->useImage($val);
            $image->delImage($raw);
        }
        return parent::onMerge($key, $raw, $val);
    }



    function getRules(): array
    {
        return [
            "state" => new VerityRule("^0|1$", "状态错误"),
        ];
    }
}