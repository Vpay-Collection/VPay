 <p align="center">
<img src="https://user-images.githubusercontent.com/37787014/227108078-5e2e2b20-1b33-440f-9cad-02a3d7a2a81d.png">
</p>


<h3 align="center">🚀 Vpay 服务端</h3>

<p align="center">
 <img src="https://img.shields.io/static/v1?label=licenes&message=GPL%20V3&color=important&style=for-the-badge"/>
 <img src="https://img.shields.io/static/v1?label=version&message=4.0.4&color=9cf&style=for-the-badge"/>
 <img src="https://img.shields.io/static/v1?label=language&message=php&color=777BB4&style=for-the-badge"/>

</p>

**⚠️⚠️⚠️警告** 
1. `master`分支版本为4.0系列全面重构的版本，可能含有BUG，请谨慎使用；
2. `dev`分支版本为3.0系列稳定版本；
3. 旧版稳定版请使用2.0系列版本，配合官方App使用。

## 简介

Vpay ——一款个人收款解决方案，使个人开发者能够安全高效地处理在线交易。


## 安装指南

### 服务端安装
> 以下三种方案三选一
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
if ( $uri ~* "^(.*)\.php$") {
rewrite ^(.*) /index.php break;
}

location / {
  try_files $uri $uri/ /index.php?$query_string;
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

### Docker部署

1. 执行docker-compose

```shell
git clone https://github.com/Vpay-Collection/Vpay
docker-compose
```
2. 按照页面提示安装，其中以下内容无需填写
- 数据库地址
- 数据库用户名
- 数据库密码
- 数据库端口
- 本站域名



### 服务端配置

1. 配置App监控端，从[vpay-android](https://github.com/Vpay-Collection/vpay-android)下载安卓客户端，扫码配置：
![img_2.png](img_2.png)
2. 配置收款码，左侧为支付宝右侧为微信：
![img_3.png](img_3.png)
3. 配置邮件通知
![img_4.png](img_4.png)

### 接入Vpay支付

参考[vpay-sdk](https://github.com/Vpay-Collection/vpay-sdk)进行接入

## 文档

[阅读文档](https://vpay.ankio.net/)


## 开源协议

GPL V3

