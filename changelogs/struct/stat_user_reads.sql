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

 Date: 29/09/2021 11:09:27
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for stat_user_reads
-- ----------------------------
DROP TABLE IF EXISTS `stat_user_reads`;
CREATE TABLE `stat_user_reads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `n8_guid` bigint(20) NOT NULL,
  `book_id` bigint(20) NOT NULL COMMENT '书籍id',
  `last_chapter_id` bigint(20) NOT NULL COMMENT '最后阅读章节id',
  `start_time` datetime NOT NULL COMMENT '开始阅读时间',
  `last_time` datetime NOT NULL COMMENT '最后阅读时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `n8_guid` (`n8_guid`,`book_id`) USING BTREE,
  KEY `start_time` (`start_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户阅读统计';

SET FOREIGN_KEY_CHECKS = 1;
