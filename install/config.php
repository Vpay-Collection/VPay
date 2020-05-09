<?php
define('APP_NAME','Vpay');
define('APP_VER','2.3');
define('APP_UPDATE','2020.05.09');
define('APP_AUTHOR','Dreamn');
define('APP_TITLE','V免签安装向导');
define('APP_URL','https://www.dreamn.cn');

/**
 * sql数据库安装请替换data/mysql.sql
 * Config替换请参考data/Config.php 记得修改数据库部分
 */
$GLOBALS['check']=array(
  'env'=>array(
      'os'=>array('min'=>'不限','good'=>'Linux'),//运行的程序的系统
      'php'=>array('min'=>'7.0','good'=>'7.3'),//php支持版本
      'upload'=>array('min'=>'2M','good'=>'2M'),//上传附件大小
      'disk'=>array('min'=>'12M','good'=>'12M'),//磁盘大小
  ),
    'var'=>array(
        'dirfile'=>array(
            array('type' => 'dir', 'path' => 'protected/tmp'),
            array('type' => 'dir', 'path' => 'install'),
            array('type' => 'file', 'path' => '/i/img/logo.png'),
        ),//检查某个目录或者文件是否可写
        'func'=>array(
            array('name' => 'json_decode'),
            array('name' => 'json_encode'),
            array('name' => 'urldecode'),
            array('name' => 'urlencode'),
            array('name' => 'openssl_encrypt'),
            array('name' => 'openssl_decrypt'),
            array('name' => 'file_get_contents'),
            array('name' => 'mb_convert_encoding'),
            array('name' => 'curl_init'),
        ),//检查某个函数是否可用
        'ext'=>array(
            array('name' => 'curl'),
            array('name' => 'openssl'),
            array('name' => 'gd'),
            array('name' => 'json'),
            array('name' => 'session'),
            array('name' => 'PDO'),
            array('name' => 'iconv'),
            array('name' => 'hash'),
            array('name' => 'mysqli')
        )//检查是否加载了对应的php拓展
    )
);
const LICENESE=<<<EOF
<h1 align="center">
                V免签 —— 个人开发者收款解决方案
            </h1>
            <div>
                <hr/>
            </div>
            <p>
                <span style="font-size:16px;">V免签(PHP - Modified By Dreamn) 是基于Speedphp4 + mysql 实现的一套免签支付程序，主要包含以下特色：</span>
            </p>
            <blockquote>
                <div align="left">
                    <ol>
                        <li>
                            <span style="font-size:16px;">收款即时到账，无需进入第三方账户，收款更安全</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">提供示例代码与核心类库简单接入</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">超简单Api使用，提供统一Api实现收款回调</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">免费、开源，无后门风险</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">支持监听店员收款信息，可使用支付宝微信小号/模拟器挂机，方便IOS用户</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">免root，免xp框架，不修改支付宝/微信客户端，防封更安全</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">如果您不熟悉PHP环境的配置，您可以使用Java版本的服务端（ </span><a
                                href="https://github.com/szvone/Vmq"><span style="font-size:16px;">https://github.com/szvone/Vmq</span></a><span
                                style="font-size:16px;"> ）</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">监控端的开源地址位于： </span><a href="https://github.com/szvone/VmqApk"><span
                                style="font-size:16px;">https://github.com/szvone/VmqApk</span></a>
                        </li>
                        <li>
                            <span style="font-size:16px;">V免签的运行环境为PHP版本&gt;=7.0。</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">V免签仅供个人开发者调试测试使用，请勿用于非法用途，商用请您申请官方商户接口</span>
                        </li>
                        <li>
                            <span style="font-size:16px;">bug反馈请建立issues或发邮件到dream@dreamn.cn</span>
                        </li>
                    </ol>
                </div>
            </blockquote>

            <hr/>
            <p>
                <br/>
            </p>
            <p align="center">
                <span style="font-size:16px;">V免签遵循 GPL License 开源协议发布，并提供免费使用，请勿用于非法用途。</span>
            </p>
            <p align="center">
                <span style="font-size:16px;">版权所有Copyright &copy; 2020 by Dreamn (</span><a href="https://dreamn.cn"><span
                    style="font-size:16px;">https://dreamn.cn</span></a><span style="font-size:16px;">)</span>
            </p>
            <p align="center">
                <span style="font-size:16px;">All rights reserved</span>
            </p>
            <p>
                <br/>
            </p>
EOF;
$GLOBALS['install']=array(//安装方式写在这里~，
    array('name'=>'完全安装','func'=>''),
    array('name'=>'最小安装','func'=>'min'),//进行处理的函数写在func，然后对应的写函数就行
);
function install_min(){//写对应的安装函数
    deldir("sdk/");
}