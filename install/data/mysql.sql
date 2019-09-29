-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-09-29 19:15:42
-- 服务器版本： 5.5.62-log
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vpay_dreamn_cn`
--

-- --------------------------------------------------------

--
-- 表的结构 `pay_appication`
--

CREATE TABLE IF NOT EXISTS `pay_appication` (
  `id` int(11) NOT NULL COMMENT ' 将作为程序的标记',
  `app_name` varchar(255) NOT NULL COMMENT 'app名称',
  `notify_url` varchar(255) NOT NULL COMMENT '异步回调接口',
  `return_url` varchar(255) NOT NULL COMMENT '同步回调接口',
  `connect_key` varchar(512) NOT NULL COMMENT '通讯密钥'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pay_order`
--

CREATE TABLE IF NOT EXISTS `pay_order` (
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

CREATE TABLE IF NOT EXISTS `pay_qrcode` (
  `id` bigint(20) NOT NULL,
  `pay_url` varchar(255) DEFAULT NULL,
  `price` double NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pay_settings`
--

CREATE TABLE IF NOT EXISTS `pay_settings` (
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
('Payof', '2'),
('State', '-1'),
('UserName', '[user]'),
('UserPassword', '[pass]'),
('ValidityTime', '5'),
('WechatPay', '');

-- --------------------------------------------------------

--
-- 表的结构 `pay_tmp_price`
--

CREATE TABLE IF NOT EXISTS `pay_tmp_price` (
  `price` varchar(255) NOT NULL,
  `oid` varchar(255) NOT NULL,
  `timeout` datetime NOT NULL COMMENT '过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pay_appication`
--
ALTER TABLE `pay_appication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pay_order`
--
ALTER TABLE `pay_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pay_id` (`pay_id`);

--
-- Indexes for table `pay_qrcode`
--
ALTER TABLE `pay_qrcode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pay_settings`
--
ALTER TABLE `pay_settings`
  ADD PRIMARY KEY (`vkey`);

--
-- Indexes for table `pay_tmp_price`
--
ALTER TABLE `pay_tmp_price`
  ADD PRIMARY KEY (`price`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pay_appication`
--
ALTER TABLE `pay_appication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' 将作为程序的标记';
--
-- AUTO_INCREMENT for table `pay_order`
--
ALTER TABLE `pay_order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pay_qrcode`
--
ALTER TABLE `pay_qrcode`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
