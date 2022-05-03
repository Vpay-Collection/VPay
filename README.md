# Vpay

![](https://img.shields.io/github/v/release/dreamncn/VPay.svg)
![](https://img.shields.io/github/issues/dreamncn/VPay)
![](https://img.shields.io/badge/PoweredBy-Ankio-f39f37)
![](https://img.shields.io/github/license/dreamncn/VPay)
![](https://img.shields.io/github/stars/dreamncn/VPay.svg?label=Stars&style=social)


## 项目简介&功能特性

Vpay3.0直接对接的支付宝当面付功能，无营业执照每天上限2k元收款。

在线演示：[这里](https://pay.ankio.net/ui/card)

接口开发文档与使用帮助: 有空补上

#### 开发建议

1. 不要自己封装处理类、处理函数，sdk中自带工具类可以直接使用
2. 默认工具类为`/src/lib/pay`

## 环境依赖

1. CentOS 7.0
2. PHP 7.4 + openssl + PDO
3. Nginx

## 伪静态

> Nignx

```
 if ( $uri ~* "^(.*)\.php$") {
        rewrite ^(.*) /index.php break;
    }
	
	location / {
       if (!-e $request_filename){
           rewrite (.*) /index.php;
       }
   }
```
> tip 如果验证码加载不出来，检查nginx配置文件中是否做了和图片相关的配置，移除jpg结尾的即可

## 部署步骤

1. 下载源代码到您的服务器
2. 修改 config/frame.yml文件中的域名为vpay的域名
3. 修改 config/db.examle.yml为config/db.yml，**db部分需要修改为你自己的数据库**
4. 访问刚才的域名进行安装
5. 安装好后登录后台进行配置当面付和邮箱
6. 配置当面付的教程有空补上




## 版本内容更新
[Changelog](CHANGELOG.md)



## 版权声明

Vpay遵循 [GPL-3.0 License](/LICENSE) 开源协议发布，并提供免费使用，请勿用于非法用途。

版权所有Copyright © 2021 by Ankio (https://ankio.net)

All rights reserved.

