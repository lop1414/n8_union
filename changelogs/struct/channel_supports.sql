/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机9.7.2
 Source Server Type    : MySQL
 Source Server Version : 50732
 Source Host           : localhost:3306
 Source Schema         : n8_union

 Target Server Type    : MySQL
 Target Server Version : 50732
 File Encoding         : 65001

 Date: 29/03/2022 10:53:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for channel_supports
-- ----------------------------
DROP TABLE IF EXISTS `channel_supports`;
CREATE TABLE `channel_supports` (
  `channel_id` bigint(20) unsigned NOT NULL,
  `admin_id` varchar(100) NOT NULL DEFAULT '' COMMENT '助手id',
  `bind_time` timestamp NULL DEFAULT NULL COMMENT '绑定时间',
  PRIMARY KEY (`channel_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道-助手关联表';

SET FOREIGN_KEY_CHECKS = 1;
