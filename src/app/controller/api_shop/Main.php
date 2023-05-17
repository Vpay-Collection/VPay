<?php

namespace app\controller\api_shop;

use Ankio\objects\PayCreateObject;
use Ankio\objects\PayNotifyObject;
use Ankio\PayConfig;
use Ankio\Vpay;
use app\database\dao\AppDao;
use app\database\dao\ShopItemDao;
use app\database\model\ShopItemModel;
use cleanphp\App;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Json;
use cleanphp\engine\EngineManager;
use cleanphp\file\Log;
use library\http\HttpClient;
use library\http\HttpException;
use library\mail\AnkioMail;

class Main extends Controller
{
    function create(): string
    {
        $id = arg("id");

       $item  = ShopItemDao::getInstance()->getById($id);

       if(empty($item)){
           return EngineManager::getEngine()->render(404,"不存在商品");
       }

        $pay_type = arg("pay_type");

        $config = new PayConfig(Config::getConfig("shop"));


        $order = new PayCreateObject();
        $order->app_item = $item->item_name;
        $order->appid = $config->id;
        $order->param = Json::encode(arg());
        $order->price = $item->item_price;
        $order->pay_type =$pay_type;
        $order->notify_url = url("api_shop","main","notify");
        $order->return_url = url("shop","main","return");

        $pay = new Vpay($config);

        $result =$pay->create($order);


        if($result===false){
            return $this->render(500,$pay->getError());
        }
        return $this->render(200,"OK",$result->url);
    }

    function notify(): string
    {
        $config = new PayConfig(Config::getConfig("shop"));
        $pay = new Vpay($config);
        $result = $pay->payNotify(function (PayNotifyObject $notifyObject){
            $data = Json::decode($notifyObject->param,true);
            $mail = $data['mail'];

            $item = ShopItemDao::getInstance()->getById($data['id']);
            $hook = $item->webhook;
            unset($data['item']);
            $title = Config::getConfig("shop")['title'];
            $app = AppDao::getInstance()->getByAppId(Config::getConfig("shop")['id']);
            if(empty($app)) {
                Log::record("Notify","回调失败，目标应用不存在");
                return ;
            }
            if(!empty($hook)){
                try {

                    $return = HttpClient::init($hook)->post($data)->setHeaders(['sign'=>$this->sign($data,$hook)])->send();
                    $json = Json::decode($return->getBody(),true);
                    if($json['code']==200){//WebHook接口响应为200表示响应成功
                        if($item->auto){
                            $file = AnkioMail::compileNotify("#4ee038", "#fff", $app->app_image, $title, "购买{$item->item_name}成功", "<p>{$json['data']}</p>");
                        }else{
                            $file = AnkioMail::compileNotify("#4ee038", "#fff", $app->app_image, $title, "购买{$item->item_name}成功", "<p>您已成功购买{$item->item_name}，请等待商家处理。</p>");
                        }
                        AnkioMail::send($mail, "购买{$item->item_name}成功", $file, $title);
                    }else{
                        $file = AnkioMail::compileNotify("#df3b3b", "#fff", $app->app_image, $title, "购买{$item->item_name}失败", "<p>您购买的{$item->item_name}出现异常，请等待商家处理。{$json['msg']}</p>");
                        AnkioMail::send($mail, "购买{$item->item_name}失败", $file, $title);
                    }
                } catch (HttpException $e) {
                    Log::record("Notify","回调响应异常：".$e->getMessage());
                    $file = AnkioMail::compileNotify("#df3b3b", "#fff", $app->app_image, $title, "购买{$item->item_name}失败", "<p>您购买的{$item->item_name}出现异常，请等待商家处理。{$e->getMessage()}</p>");
                    AnkioMail::send($mail, "购买{$item->item_name}失败", $file, $title);
                }

            }else{

                $file = AnkioMail::compileNotify("#4ee038", "#fff", $app->app_image, $title, "购买{$item->item_name}成功", "<p>您已成功购买{$item->item_name}，请等待商家处理。</p>");
                AnkioMail::send($mail, "购买{$item->item_name}成功", $file, $title);
            }

        });
        if($result){
            App::exit("回调成功退出");
        }
        return $this->render(500,$pay->getError());
    }

    private function sign($array,$key): string
    {
        ksort($array);
        return hash_hmac('sha256', http_build_query($array), $key);
    }


}