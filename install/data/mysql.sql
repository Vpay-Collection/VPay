-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-05-04 14:51:43
-- 服务器版本： 5.5.62-log
-- PHP 版本： 7.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `a_com`
--

-- --------------------------------------------------------

--
-- 表的结构 `pay_appication`
--

CREATE TABLE `pay_appication` (
  `id` int(11) NOT NULL COMMENT ' 将作为程序的标记',
  `app_name` varchar(255) NOT NULL COMMENT 'app名称',
  `notify_url` varchar(255) NOT NULL COMMENT '异步回调接口',
  `return_url` varchar(255) NOT NULL COMMENT '同步回调接口',
  `connect_key` varchar(512) NOT NULL COMMENT '通讯密钥'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pay_appication`
--

INSERT INTO `pay_appication` (`id`, `app_name`, `notify_url`, `return_url`, `connect_key`) VALUES
(1, '内置商城', 'http://[url]/index/buy/notify', 'http://[url]/index/buy/return', 'YtKWARQRpKDCtQs88cC3Finnic5d7iGasGiHwecyZsPM63MHHrp2GCZEpJesYn5kZpWNmGNDMszBwCb4Sb2iGiJPzT6iG8h34szenda7DeMdDfh5yZ3cNRBTFFA8Y6WZ');

-- --------------------------------------------------------

--
-- 表的结构 `pay_order`
--

CREATE TABLE `pay_order` (
  `id` bigint(20) NOT NULL,
  `close_date` bigint(20) NOT NULL,
  `create_date` bigint(20) NOT NULL,
  `paytype` int(11) NOT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `param` text,
  `pay_date` bigint(20) NOT NULL,
  `pay_id` varchar(255) DEFAULT NULL,
  `price` double NOT NULL,
  `really_price` double NOT NULL,
  `state` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `appid` int(11) NOT NULL COMMENT '由哪个应用创建的',
  `payUrl` text NOT NULL,
  `isAuto` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- 表的结构 `pay_qrcode`
--

CREATE TABLE `pay_qrcode` (
  `id` bigint(20) NOT NULL,
  `pay_url` varchar(255) DEFAULT NULL,
  `price` double NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- 表的结构 `pay_settings`
--

CREATE TABLE `pay_settings` (
  `vkey` varchar(255) NOT NULL,
  `vvalue` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pay_settings`
--

INSERT INTO `pay_settings` (`vkey`, `vvalue`) VALUES
('Ailuid', ''),
('AliPay', ''),
('Key', ''),
('LastHeart', ''),
('LastLogin', ''),
('LastPay', ''),
('MailNoticeMe', 'on'),
('MailNoticeYou', 'on'),
('MailPass', ''),
('MailPort', ''),
('MailRec', ''),
('MailSend', ''),
('MailSmtp', ''),
('Payof', '2'),
('State', '-1'),
('UserName', '[user]'),
('UserPassword', '[pass]'),
('UseShop', '0'),
('ValidityTime', '5'),
('WechatPay', '');

-- --------------------------------------------------------

--
-- 表的结构 `pay_shop`
--

CREATE TABLE `pay_shop` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` varchar(64) DEFAULT NULL,
  `msg` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pay_shop`
--

INSERT INTO `pay_shop` (`id`, `name`, `price`, `msg`) VALUES
(1, '测试商品', '0.1', '您已支付成功<br> \n订单号：{payId} <br>\n支付方式：{type} <br>\n支付金额：￥{reallyPrice} <br>\n站长将在24小时内处理，如有疑问请联系dream@dreamn.cn');

-- --------------------------------------------------------

--
-- 表的结构 `pay_tmp_price`
--

CREATE TABLE `pay_tmp_price` (
  `price` varchar(255) NOT NULL,
  `oid` varchar(255) NOT NULL,
  `timeout` bigint(20) NOT NULL COMMENT '过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `pay_appication`
--
ALTER TABLE `pay_appication`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `pay_order`
--
ALTER TABLE `pay_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pay_id` (`pay_id`);

--
-- 表的索引 `pay_qrcode`
--
ALTER TABLE `pay_qrcode`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `pay_settings`
--
ALTER TABLE `pay_settings`
  ADD PRIMARY KEY (`vkey`);

--
-- 表的索引 `pay_shop`
--
ALTER TABLE `pay_shop`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `pay_tmp_price`
--
ALTER TABLE `pay_tmp_price`
  ADD PRIMARY KEY (`price`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `pay_appication`
--
ALTER TABLE `pay_appication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' 将作为程序的标记', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `pay_order`
--
ALTER TABLE `pay_order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `pay_qrcode`
--
ALTER TABLE `pay_qrcode`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- 使用表AUTO_INCREMENT `pay_shop`
--
ALTER TABLE `pay_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
