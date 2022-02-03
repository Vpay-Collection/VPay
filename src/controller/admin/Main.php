<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Main
 * Created By ankio.
 * Date : 2022/1/29
 * Time : 11:40 下午
 * Description :
 */

namespace app\controller\admin;

use app\core\config\Config;
use app\core\utils\FileUtil;
use app\core\web\Session;
use app\lib\Upload\FileUpload;

class Main extends BaseController
{
    function nav(): array
    {
        $settings = [
            [
                "title" => "用户设置",
                "fontFamily" => "ok-icon",
                "href" => "setting/user",
                "icon" => "&#xe736;",
                "spread" => false
            ],
            [
                "title" => "网站设置",
                "fontFamily" => "ok-icon",
                "href" => "setting/site",
                "icon" => "&#xe73a;",
                "spread" => false
            ],
            [
                "title" => "支付配置",
                "fontFamily" => "ok-icon",
                "href" => "setting/order",
                "icon" => "&#xe673;",
                "spread" => false
            ],
            [
                "title" => "邮件设置",
                "fontFamily" => "ok-icon",
                "href" => "setting/mail",
                "icon" => "&#xe7bd;",
                "spread" => false
            ],
        ];
        if(Config::getInstance("pay")->getOne("login")=="ankio"){
            unset($settings[0]);
            $settings = array_values($settings);
        }
        return $this->ret(200,null,[
            [
                "title" => "控制台",
                "href" => "/",
                "fontFamily" => "ok-icon",
                "icon" => "&#xe654;",
                "spread" => true,
                "isCheck" => true
            ],
            [
                "title" => "系统配置",
                "href" => "",
                "fontFamily" => "ok-icon",
                "icon" => "&#xe68a;",
                "spread" => false,
                "children" => $settings
            ],
            [
                "title" => "应用管理",
                "fontFamily" => "ok-icon",
                "href" => 'app/list',
                "icon" => "&#xe729;",
                "spread" => false
            ],
            [
                "title" => "订单列表",
                "fontFamily" => "ok-icon",
                "href" => 'order',
                "icon" => "&#xe7d1;",
                "spread" => false
            ],
            [
                "title" => "内置发卡",
                "href" => "",
                "fontFamily" => "ok-icon",
                "icon" => "&#xe6ee;",
                "spread" => false,
                "children" => [
                    [
                        "title" => "系统配置",
                        "fontFamily" => "ok-icon",
                        "href" => "shop/setting",
                        "icon" => "&#xe68a;",
                        "spread" => false
                    ],
                    [
                        "title" => "卡密管理",
                        "fontFamily" => "ok-icon",
                        "href" => "shop/list",
                        "icon" => "&#xe6ee;",
                        "spread" => false
                    ],
                ]
            ],
            [
                "title" => "开发文档",
                "href" =>'',
                "fontFamily" => "ok-icon",
                "icon" => "&#xe791;",
                "spread" => false
            ]
        ]);
    }

    function notice(): array
    {
        return $this->ret(200,"",["version"=>0,"data"=>"欢迎使用AnkioのVpay！"]);
    }
    function userInfo(): array
    {
        return $this->ret(200,"",["img"=>Session::getInstance()->get("img"),"nickName"=>Session::getInstance()->get("nickName")]);
    }

    function img(){
        $upload  = new FileUpload();
        $upload->set("path",APP_PUBLIC."ui".DS."static".DS."img");
      $result =   $upload->upload("file");
      if($result){
          $file = APP_PUBLIC."ui".DS."static".DS."img".DS."face.jpg";
          rename($upload->getFilePath(),$file);
          return $this->ret(200);
      }
      return $this->ret(403,$upload->getErrorMsg());
    }
}