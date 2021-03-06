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

 Date: 06/03/2021 16:36:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for n8_union_users
-- ----------------------------
DROP TABLE IF EXISTS `n8_union_users`;
CREATE TABLE `n8_union_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `n8_guid` varchar(64) NOT NULL,
  `channel_id` int(11) NOT NULL COMMENT '渠道ID',
  `created_time` datetime NOT NULL COMMENT '创建时间',
  `cp_book_id` varchar(64) DEFAULT NULL COMMENT '平台书籍ID',
  `cp_chapter_id` varchar(64) DEFAULT NULL COMMENT '平台章节ID',
  `cp_force_chapter_id` varchar(64) DEFAULT NULL COMMENT '平台强制章节ID',
  `admin_id` int(11) DEFAULT NULL,
  `click_id` bigint(20) DEFAULT '0' COMMENT '点击ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `n8_guid` (`n8_guid`,`channel_id`) USING BTREE,
  KEY `created_time` (`created_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='联运用户';

SET FOREIGN_KEY_CHECKS = 1;
