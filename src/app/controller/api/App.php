<?php

namespace app\controller\api;

use app\database\dao\OrderDao;
use app\exception\OrderNotFoundException;
use app\objects\app\HeartObject;
use app\objects\app\PushObject;
use cleanphp\base\Config;
use cleanphp\base\Json;
use cleanphp\cache\Cache;
use cleanphp\file\Log;
use library\verity\VerityException;

class App extends BaseController
{
    private string $key = "";

    public function __init(): void
    {
        $this->key = Config::getConfig('app')['app_key'];
    }

    function heart(): string
    {
        try {
            $heart = new HeartObject(get(), $this->key);
            Cache::init()->set("last_heart", time());
            return $this->json(200, "心跳成功");
        } catch (VerityException $exception) {
            Log::record("app_channel", "心跳异常：" . $exception->getMessage());
            return $this->json(400, $exception->getMessage());
        }
    }

    function push(): string
    {
        Log::record("app_channel", "收到App推送：" . Json::encode(arg()));
        try {
            $push = new PushObject(get(), $this->key);
        } catch (VerityException $exception) {
            return $this->json(400, $exception->getMessage());
        }
        $result = OrderDao::getInstance()->getWaitOrderByPayType($push->type, $push->price);
        if (empty($result)) {
            return $this->json(500, '无订单待确认！');
        }
        try {
            OrderDao::getInstance()->notify($result->order_id, $this->key);
        } catch (OrderNotFoundException $e) {
            return $this->json(500, '无订单待确认！');
        }
        return $this->json(200);
    }
}