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

 Date: 12/04/2021 11:30:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tmp_user_read_actions
-- ----------------------------
DROP TABLE IF EXISTS `tmp_user_read_actions`;
CREATE TABLE `tmp_user_read_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `n8_guid` varchar(64) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `action_time` datetime NOT NULL COMMENT '行为时间',
  `cp_book_id` varchar(64) NOT NULL DEFAULT '0' COMMENT '平台书籍id',
  `cp_chapter_id` varchar(64) NOT NULL DEFAULT '0' COMMENT '平台章节id',
  `adv_alias` varchar(50) DEFAULT NULL COMMENT '广告商标识',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `n8_guid` (`n8_guid`,`action_time`) USING BTREE,
  KEY `action_time` (`action_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户阅读行为表';

SET FOREIGN_KEY_CHECKS = 1;
