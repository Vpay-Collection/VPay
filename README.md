# V免签 PHP版

![](https://img.shields.io/github/v/release/dreamncn/VPay.svg)
![](https://img.shields.io/github/issues/dreamncn/VPay)
![](https://img.shields.io/badge/PoweredBy-Dreamn-f39f37)
![](https://img.shields.io/github/license/dreamncn/VPay)
![](https://img.shields.io/github/stars/dreamncn/VPay.svg?label=Stars&style=social)

[原作者的PHP版VPay](https://github.com/szvone/vmqphp)

## 项目简介&功能特性

V免签(PHP) 是基于SpeedPHP(魔改版) + mysql 实现的一套免签支付程序，主要包含以下特色：

```
    1. 收款即时到账，无需进入第三方账户，收款更安全
    2. 提供示例代码（demo）简单接入
    3. 免费、开源，无后门风险
    4. 支持监听店员收款信息，可使用支付宝微信小号/模拟器挂机，方便IOS用户
    5. 免root，免xp框架，不修改支付宝/微信客户端，防封更安全
    6. 如果您不熟悉PHP环境的配置，您可以使用Java版本的服务端
```

JAVA版的开源地址位于：[这里](https://github.com/szvone/Vmq)

监控端的开源地址位于：[这里](https://github.com/szvone/VmqApk)

#### 原理

用户扫码付款 -> 收到款项后手机通知栏会有提醒 -> V免签监控端监听到提醒，推送至服务端->服务端根据金额判断是哪笔订单

## 环境依赖

1. CentOS 7.0
2. PHP 7.3 + openssl + PDO
3. Apache （开启伪静态）/ Nginx(需要自行修改伪静态文件)

## 部署步骤

1. 下载源代码到您的服务器
2. 然后访问服务器公网IP开始安装
3. 如果您是第一次使用建议您选择完全安装
4. 安装好后登录后台进行配置
5. 打开网站后台监控端设置
6. 下载监控端[点击这里](https://github.com/szvone/vmqApk/releases)
7. 安装监控端后使用手动配置或扫码配置
8. 监控端中点击开启服务跳转到辅助功能中开启服务
9. 开启服务后返回v免签点击检测监听权限
10. 如果显示监听权限正常，至此安装完毕，如果只收到通知栏推送的测试通知，则系统不兼容无法正常监听
11. 如果显示监听权限正常，还是无法正常运行，那么请确定微信是否关注 “微信支付” 和 “微信收款助手” 这两个公众号

### V免签客户端设置步骤

（教程为MIUI系统，非MIUI系统请参考教程进行设置）

1. 关闭系统神隐模式

   - （旧版MIUI系统）在系统【设置】 - 【其他高级设置】 - 【电量与性能】 - 【神隐模式】 - 【V免签监控端】设置为关闭

   - （新版MIUI系统）在系统【设置】 - 【其他高级设置】 - 【电量与性能】 - 【省电优化】 - 【应用智能省电】，将V免签监控端、微信、支付宝的3个APP全都改为无限制

2. 添加内存清理白名单

3. 关闭WIFI优化

    - （旧版MIUI系统）在系统【设置】 - 【WLAN】 -【高级设置】 -【WLAN优化】，关闭它。

    - （新版MIUI系统）在系统【设置】 - 【WLAN】 -【高级设置】 - 【在休眠状态下保持WLAN网络连接】改为“始终”

4. 开启推送通知

    - 系统【设置】 - 【通知和状态栏】 - 【通知管理】中，找到这3个App，把里面的开关全部打开

    - 在微信的【设置】 - 【勿扰模式】中，关闭勿扰模式

    - 在微信的公众号，关注 【微信收款助手】 这个公众号

    - 在支付宝的主页，上方搜索框 搜索 【支付助手】 ，进入支付助手，右上角小齿轮，打开【接收付款消息提醒】



## 目录结构描述
```
|____i                              资源目录
|____index.php                      所有文件的入口文件
|____README.md                      本文件
|____protected                      保护目录（禁止直接访问的）
| |____.htaccess                    apache伪静态文件
| |____config.php                   该程序的配置文件
| |____controller                   程序控制器目录
| | |____BaseController.php         基类控制器
| | |____MainController.php         主控（第一个访问的页面）
| | |____AppController.php          app推送数据的控制器
| | |____admin                      管理员目录控制器
| | | |____BaseController.php       基类控制器
| | | |____MainController.php				主控（管理员目录）
| | | |____MenuController.php				菜单控制
| | | |____ApiController.php				后台api
| | |____api
| | | |____ApiController.php        对外提供的api
| | | |____BaseController.php				api基类
| | | |____PayController.php				api的支付接口
| |____include											额外的类库
| | |____Web.php										封装好的web类
| | |____AlipaySign.php							阿里签名类
| | |____Des.php										des加密类（openssl支持）
| |____lib													核心类库
| | |____speed.php									程序驱动
| | |____QrCode											二维码支持库
| |____model												系统功能模块
| | |____User.php										用户模块
| | |____Config.php									系统配置模块
| | |____Main.php										后台主控模块
| | |____PayCode.php								二维码模块
| | |____Order.php									订单模块
| | |____App.php										app模块
| | |____Temp.php										临时模块
| |____tmp													前端渲染的临时目录
| |____view													前端模板目录
| | |____index.html                 前端index
| | |____admin                      后台模板目录
| | | |____zfbqrcodelist.html				支付宝二维码列表
| | | |____wxqrcodelist.html				微信二维码列表
| | | |____setting.html							设置
| | | |____orderlist.html						订单列表
| | | |____jk.html									监控端信息
| | | |____addzfbqrcode.html				添加支付宝二维码
| | | |____addwxqrcode.html					添加微信二维码
| | | |____index.html								后台框架
| | | |____main.html								后台首页
| | | |____api.html									api页面
| | | |____addapplication.html			添加应用
| | | |____applicationlist.html			应用列表
| | |____error.html									系统错误目录
| | |____pay												自带的支付页面目录
| | | |____pay.html									支付页面
|____.htaccess											apache伪静态文件
|____robots.txt											爬虫文件
|____demo														自带的demo支付程序
| |____index.php										demo的首页
| |____go.php												demo的支付跳转
| |____notify.php										demo的异步回调
| |____core													demo的核心库
| | |____lib												核心库
| | | |____Web.php									封装好的web类
| | | |____AlipaySign.php						阿里签名
| | |____config.php									demo相关配置文件
| | |____Vpay.php										VPay类文件
| |____return.php										demo的同步回调
| |____readme.md
|____install												安装目录
| |____readme.md										
| |____css													资源目录
| |____data													安装的数据部分目录
| | |____mysql.sql									数据库
| | |____config.php									基础配置文件
| |____images												资源目录
| |____include										
| | |____var.php										要检查的目录、函数、拓展
| | |____function.php								检查目录函数等
| |____js														资源目录
| |____index.php										安装首页
| |____.htaccess										apache伪静态
| |____views												html模板目录
|____LICENSE												开源协议
```

## 版本内容更新

####  Ver. 1.0.0.1（2019.09.29）

1、修改后台登录部分密码传输与密码加密存储方案（防止中间人）

2、修改sign的最终计算方案（MD5->sha256）

3、优化了后端数据库，删除精简不必要的字段、规范后端模块命名与编写、增加大量注释便于修改

4、自定义参数部分默认进行url解码与unicode解码，方便查看

5、对demo部分封装新的类库，方便直接调用

6、删掉没必要的api接口的教程

7、对支付结果增加校验，便于了解支付结果

####  Ver. 1.0.0.0（2019.06.23）

1、原作者的php版本后台安全性有点脆弱，主要是是后台登录校验部分

2、而且采用了一个非常臃肿的thinkphp框架，上传速率慢

3、给原作者增加安装程序，用以安装该项目

4、对于其支付宝支付部分稍作修改增强用户体验

5、修改回调方式

6、采用全新修改的sp框架重新编写，速度更快

7、更换sign计算方式

## 鸣谢

- [V免签](https://github.com/szvone/Vmq)
- [SpeedPHP框架](https://github.com/SpeedPHP/manual)


## 版权声明

V免签 - Dreamn修改版遵循 [MIT License](/LICENSE) 开源协议发布，并提供免费使用，请勿用于非法用途。

版权所有Copyright © 2019 by dreamn (https://dreamn.cn)

All rights reserved。

