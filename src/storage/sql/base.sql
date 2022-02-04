-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2022-02-04 14:17:16
-- 服务器版本： 5.5.62-log
-- PHP 版本： 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `autotest_ankio_n`
--

-- --------------------------------------------------------

--
-- 表的结构 `pay_application`
--

DROP TABLE IF EXISTS `pay_application`;
CREATE TABLE `pay_application` (
  `id` int(11) NOT NULL COMMENT ' 将作为程序的标记',
  `app_name` varchar(255) NOT NULL COMMENT 'app名称',
  `connect_key` varchar(512) NOT NULL COMMENT '通讯密钥'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 插入之前先把表清空（truncate） `pay_application`
--

TRUNCATE TABLE `pay_application`;
--
-- 转存表中的数据 `pay_application`
--

INSERT INTO `pay_application` (`id`, `app_name`, `connect_key`) VALUES
(5, 'Vpay内置发卡', 'P4HAWM4CRYpsaTTsH85ft78cMnMyRCAZt6h88MBxyCP3SC4KTtR33XN4C7mxMW3z');

-- --------------------------------------------------------

--
-- 表的结构 `pay_order`
--

DROP TABLE IF EXISTS `pay_order`;
CREATE TABLE `pay_order` (
  `id` bigint(20) NOT NULL,
  `close_date` bigint(20) NOT NULL,
  `create_date` bigint(20) NOT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `param` text,
  `pay_date` bigint(20) NOT NULL,
  `pay_id` varchar(255) DEFAULT NULL,
  `price` double NOT NULL,
  `state` int(11) NOT NULL,
  `appid` int(11) NOT NULL COMMENT '由哪个应用创建的',
  `isAuto` tinyint(1) NOT NULL DEFAULT '0',
  `really_price` double NOT NULL,
  `title` text NOT NULL,
  `img` longtext NOT NULL,
  `notifyUrl` text NOT NULL,
  `returnUrl` text NOT NULL,
  `userId` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 插入之前先把表清空（truncate） `pay_order`
--

TRUNCATE TABLE `pay_order`;
-- --------------------------------------------------------

--
-- 表的结构 `pay_shop`
--

DROP TABLE IF EXISTS `pay_shop`;
CREATE TABLE `pay_shop` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `msg` text NOT NULL,
  `params` text NOT NULL,
  `img` text NOT NULL,
  `code` text NOT NULL,
  `price` double NOT NULL,
  `isCode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 插入之前先把表清空（truncate） `pay_shop`
--

TRUNCATE TABLE `pay_shop`;
--
-- 转存表中的数据 `pay_shop`
--

INSERT INTO `pay_shop` (`id`, `title`, `description`, `msg`, `params`, `img`, `code`, `price`, `isCode`) VALUES
(5, '卡片短信高级版', '请手动输入机器码后购买授权。', '<p>您的激活码为：{card}</p>\n<p>感谢您对《卡片短信》的支持！</p>', '  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">邮箱</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"mail\" required  lay-verify=\"required|mail\" placeholder=\"请输入您的邮箱\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">优惠券</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"discount\"  placeholder=\"请输入优惠券码（没有请置空）\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n</div>\n  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">机器码</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"code\" required  lay-verify=\"required\" placeholder=\"请输入卡片短信机器码\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n', '/ui/img/4ed36cc128013e08093325075a799937.png', 'return sha1(md5($arg[\"mail\"]).md5($arg[\"code\"]).md5(\"ankio_2022\"));\n', 0.1, 1),
(6, 'Vpay问题咨询', '填写问题后，再进行支付。', '<p>您咨询的问题已经发送给作者，请耐心等待回复。</p>\n<p>Question:</p>\n<p>{question}</p>', '  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">邮箱</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"mail\" required  lay-verify=\"required|mail\" placeholder=\"请输入您的邮箱\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">优惠券</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"discount\"  placeholder=\"请输入优惠券码（没有请置空）\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n</div>\n \n\n  <div class=\"layui-form-item layui-form-text\">\n    <label class=\"layui-form-label\">咨询的问题</label>\n    <div class=\"layui-input-block\">\n      <textarea name=\"question\" placeholder=\"请输入问题\" class=\"layui-textarea\"></textarea>\n    </div>\n  </div>\n', '/ui/img/51179bfac48c9345e96314e1b3ca1d0a.png', 'return \'\';', 10, 1),
(7, 'Vpay远程安装', 'Vpay远程安装，请提供以下信息！', '<p>您的安装要求已提交，请等待站长处理~</p>\n<p>绑定域名：{domain}</p>\n<p>您的宝塔面板：{url}</p>\n<p>您的账号：{account}</p>\n<p>您的密码：{passwd}</p>\n<p>您的联系邮箱：{mail}</p>\n<p>您的说明信息：{remark}</p>', '  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">邮箱</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"mail\" required  lay-verify=\"required|mail\" placeholder=\"请输入您的邮箱\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">优惠券</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"discount\"  placeholder=\"请输入优惠券码（没有请置空）\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n</div>\n\n\n  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">宝塔地址</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"url\" required  lay-verify=\"required\" placeholder=\"请输入输入宝塔面板地址\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">登录账号</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"account\" required  lay-verify=\"required\" placeholder=\"请输入登录账号\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">登录密码</label>\n    <div class=\"layui-input-block\">\n      <input type=\"password\" name=\"passwd\" required  lay-verify=\"required\" placeholder=\"请输入登录密码\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">绑定域名</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"domain\" required  lay-verify=\"required\" placeholder=\"请输入绑定域名\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n  <div class=\"layui-form-item layui-form-text\">\n    <label class=\"layui-form-label\">其他要求/说明</label>\n    <div class=\"layui-input-block\">\n      <textarea name=\"remark\" placeholder=\"请输入其他要求和说明\" class=\"layui-textarea\"></textarea>\n    </div>\n  </div>\n', '/ui/img/a3ad4e171489f56a87f07389eae5356b.png', 'return \'\';', 60, 1),
(8, '测试卡密分发功能', '测试卡密', '<p>您的卡密为{card}</p>', '  <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">邮箱</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"mail\" required  lay-verify=\"required|mail\" placeholder=\"请输入您的邮箱\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n  </div>\n\n <div class=\"layui-form-item\">\n    <label class=\"layui-form-label\">优惠券</label>\n    <div class=\"layui-input-block\">\n      <input type=\"text\" name=\"discount\"  placeholder=\"请输入优惠券码（没有请置空）\" autocomplete=\"off\" class=\"layui-input\">\n    </div>\n</div>', '/ui/img/1bff251b9bb7a35510663c9017b5960b.png', '', 0.01, 0);

-- --------------------------------------------------------

--
-- 表的结构 `pay_shop_item`
--

DROP TABLE IF EXISTS `pay_shop_item`;
CREATE TABLE `pay_shop_item` (
  `id` int(11) NOT NULL,
  `code` text NOT NULL,
  `shopId` int(11) NOT NULL,
  `lockItem` bigint(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 插入之前先把表清空（truncate） `pay_shop_item`
--

TRUNCATE TABLE `pay_shop_item`;
--
-- 转储表的索引
--

--
-- 表的索引 `pay_application`
--
ALTER TABLE `pay_application`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `pay_order`
--
ALTER TABLE `pay_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pay_id` (`pay_id`);

--
-- 表的索引 `pay_shop`
--
ALTER TABLE `pay_shop`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `pay_shop_item`
--
ALTER TABLE `pay_shop_item`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `pay_application`
--
ALTER TABLE `pay_application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' 将作为程序的标记', AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `pay_order`
--
ALTER TABLE `pay_order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- 使用表AUTO_INCREMENT `pay_shop`
--
ALTER TABLE `pay_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `pay_shop_item`
--
ALTER TABLE `pay_shop_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
