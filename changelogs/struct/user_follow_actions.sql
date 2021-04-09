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

 Date: 09/04/2021 11:36:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user_follow_actions
-- ----------------------------
DROP TABLE IF EXISTS `user_follow_actions`;
CREATE TABLE `user_follow_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `n8_guid` bigint(20) NOT NULL,
  `action_time` datetime NOT NULL COMMENT '行为时间',
  `adv_alias` varchar(50) DEFAULT NULL COMMENT '广告商标识',
  `click_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '点击ID',
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `ip` varchar(15) DEFAULT NULL COMMENT 'IP',
  `ua` text,
  `request_id` varchar(255) DEFAULT NULL COMMENT '广告商的请求ID',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `n8_guid` (`n8_guid`,`action_time`) USING BTREE,
  KEY `action_time` (`action_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户关注行为表';

SET FOREIGN_KEY_CHECKS = 1;
