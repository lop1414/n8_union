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

 Date: 11/03/2021 11:14:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for channels
-- ----------------------------
DROP TABLE IF EXISTS `channels`;
CREATE TABLE `channels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `adv_alias` varchar(50) NOT NULL COMMENT '广告商标识',
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `gcid` int(11) DEFAULT NULL COMMENT '全局CP渠道ID',
  `status` varchar(50) NOT NULL COMMENT '状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cp_channel_id` (`product_id`,`gcid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道';

SET FOREIGN_KEY_CHECKS = 1;
