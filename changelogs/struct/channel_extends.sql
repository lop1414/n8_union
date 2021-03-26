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

 Date: 26/03/2021 09:42:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for channel_extends
-- ----------------------------
DROP TABLE IF EXISTS `channel_extends`;
CREATE TABLE `channel_extends` (
  `channel_id` bigint(20) unsigned NOT NULL,
  `adv_alias` varchar(50) NOT NULL COMMENT '广告商标识',
  `status` varchar(50) NOT NULL COMMENT '状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`channel_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道扩展';

SET FOREIGN_KEY_CHECKS = 1;
