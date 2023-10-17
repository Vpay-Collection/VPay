 <p align="center">
<img src="./image/cover.png">
</p>


<h3 align="center">🚀 Vpay 服务端</h3>

<p align="center">
 <img src="https://img.shields.io/static/v1?label=licenes&message=GPL%20V3&color=important&style=for-the-badge"/>
 <img src="https://img.shields.io/static/v1?label=version&message=4.2.0&color=9cf&style=for-the-badge"/>
 <img src="https://img.shields.io/static/v1?label=language&message=php&color=777BB4&style=for-the-badge"/>

</p>

### ️⚠️ 警告 

- 手机二维码监听收款是异地收款，收款频率高可能导致风控；
- 云端监听收款风险同上；

## 按需选择

1. `master`分支版本为4.0系列全面重构的版本，仅支持`支付宝当面付`；
2. `dev`分支版本为[3.0系列](https://github.com/Vpay-Collection/VPay/releases/tag/3.1)稳定版本，仅支持`支付宝当面付`；
3. 旧版稳定版请使用[2.0系列](https://github.com/Vpay-Collection/VPay/releases/tag/2.4)版本，配合[官方App](https://github.com/Vpay-Collection/vmqApk/releases/tag/v1.8.2)使用，支持手机端二维码监听收款。

## 简介

Vpay ——一款个人收款解决方案，使个人开发者能够安全高效地处理在线交易。


## 安装指南

### 服务端安装
> 以下两种方案三选一
#### 一、宝塔面板一键部署

1. 导入项目到宝塔面板
![img.png](img.png)
2. 点击一键部署，填入域名后点提交即可
![img_1.png](img_1.png)

#### 二、手动部署

1. 导入项目到网站文件夹下
2. 配置运行目录为`/public`
3. 配置伪静态
```
if ($uri ~* "^(.*)\.php$") {
    rewrite ^(.*) /cleanphp/bootstrap.php last;
}

location @cleanphp {
    rewrite ^ /cleanphp/bootstrap.php last;
}

location ~* ^\/clean_static\/(.*)$ {
    if_modified_since before;
    try_files /app/public/$1 @cleanphp;
}

location / {
    try_files /app/storage/public/$uri @cleanphp;
}


```
4. 如果使用宝塔面板部署，请**务必删除**宝塔默认配置的以下配置文件
```
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
        error_log /dev/null;
        access_log /dev/null;
    }

    location ~ .*\.(js|css)?$
    {
        expires      12h;
        error_log /dev/null;
        access_log /dev/null;
    }
```



### 服务端配置

1. 申请[支付宝当面付](https://open.alipay.com/intergraAssist/SC00002242?projectId=1487001107)
- 【扫码支付】-【自研】，根据页面提示的接入准备完成当面付接入
2. 填写当面付信息
3. 配置邮件通知
![img_4.png](img_4.png)

### 接入Vpay支付

参考[vpay-sdk](https://github.com/Vpay-Collection/vpay-sdk)进行接入

## 文档

[阅读文档](https://vpay.ankio.net/)


## 开源协议

GPL V3

