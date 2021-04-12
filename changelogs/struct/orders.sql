/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机 192.168.10.10
 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : localhost:3306
 Source Schema         : n8_union

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 12/04/2021 14:20:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `n8_goid` bigint(20) NOT NULL,
  `n8_guid` bigint(20) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT '产品id',
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道id',
  `order_time` datetime NOT NULL COMMENT '订单时间',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '金额 单位/分',
  `type` varchar(50) NOT NULL COMMENT '类型',
  `status` varchar(50) NOT NULL COMMENT '状态',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `adv_alias` varchar(50) DEFAULT NULL COMMENT '广告商标识',
  `click_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单点击ID',
  `complete_click_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '完成点击ID',
  `order_times` int(11) NOT NULL COMMENT '第几次下单',
  `complete_times` int(11) NOT NULL COMMENT '第几次充值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`n8_goid`),
  UNIQUE KEY `n8_guid` (`n8_guid`) USING BTREE,
  KEY `order_time` (`order_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值订单';

SET FOREIGN_KEY_CHECKS = 1;
