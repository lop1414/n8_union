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

 Date: 15/04/2021 20:32:57
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for multi_platform_channel
-- ----------------------------
DROP TABLE IF EXISTS `multi_platform_channel`;
CREATE TABLE `multi_platform_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `adv_alias` varchar(50) NOT NULL COMMENT '广告商标识',
  `android_channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `ios_channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `status` varchar(50) NOT NULL COMMENT '状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `android_channel_id` (`android_channel_id`) USING BTREE,
  KEY `ios_channel_id` (`ios_channel_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=utf8 COMMENT='多平台渠道';

SET FOREIGN_KEY_CHECKS = 1;
