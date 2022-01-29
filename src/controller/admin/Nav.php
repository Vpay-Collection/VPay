<?php

namespace app\controller\admin;

class Nav extends BaseController
{
    function list(){
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
                "children" => [
                    [
                        "title" => "用户设置",
                        "fontFamily" => "ok-icon",
                        "href" => "pages/setting/user.html",
                        "icon" => "&#xe736;",
                        "spread" => false
                    ],
                    [
                        "title" => "收款二维码设置",
                        "fontFamily" => "ok-icon",
                        "href" => "pages/setting/qrcode.html",
                        "icon" => "&#xe7ce;",
                        "spread" => false
                    ],
                    [
                        "title" => "订单和支付配置",
                        "fontFamily" => "ok-icon",
                        "href" => "pages/setting/order.html",
                        "icon" => "&#xe673;",
                        "spread" => false
                    ],
                    [
                        "title" => "客户端设置",
                        "fontFamily" => "ok-icon",
                        "href" => "pages/setting/app.html",
                        "icon" => "&#xe729;",
                        "spread" => false
                    ],
                    [
                        "title" => "邮件设置",
                        "fontFamily" => "ok-icon",
                        "href" => "pages/setting/mail.html",
                        "icon" => "&#xe7bd;",
                        "spread" => false
                    ],
                ]
            ],
            [
                "title" => "应用管理",
                "fontFamily" => "ok-icon",
                "href" => 'pages/app/list.html',
                "icon" => "&#xe729;",
                "spread" => false
            ],
              [
                  "title" => "订单列表",
                  "fontFamily" => "ok-icon",
                  "href" => 'pages/order/list.html',
                  "icon" => "&#xe7d1;",
                  "spread" => false
              ],
              [
                  "title" => "二维码管理",
                  "fontFamily" => "ok-icon",
                  "href" => "",
                  "icon" => "&#xe6b0;",
                  "spread" => false,
                  "children" => [
                      [
                          "title" => "微信二维码",
                          "fontFamily" => "ok-icon",
                          "href" => "pages/qrcode/wechat.html",
                          "icon" => "&#xe70c;",
                          "spread" => false
                      ],
                      [
                          "title" => "支付宝二维码",
                          "fontFamily" => "ok-icon",
                          "href" => "pages/qrcode/alipay.html",
                          "icon" => "&#xe61a;",
                          "spread" => false
                      ],
                  ]
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
                          "href" => "pages/fk/system.html",
                          "icon" => "&#xe68a;",
                          "spread" => false
                      ],
                      [
                          "title" => "卡密管理",
                          "fontFamily" => "ok-icon",
                          "href" => "pages/fk/km.html",
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

}