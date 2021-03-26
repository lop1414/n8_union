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

 Date: 26/03/2021 09:42:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for channels
-- ----------------------------
DROP TABLE IF EXISTS `channels`;
CREATE TABLE `channels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `cp_channel_id` varchar(50) NOT NULL COMMENT 'CP渠道ID',
  `name` varchar(60) NOT NULL COMMENT 'CP渠道名称',
  `book_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '书籍id',
  `chapter_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '章节id',
  `force_chapter_id` bigint(20) DEFAULT NULL COMMENT '强制章节id',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '平台创建时间',
  `updated_time` timestamp NULL DEFAULT NULL COMMENT '平台更改时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cp_channel_id` (`product_id`,`cp_channel_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道';

SET FOREIGN_KEY_CHECKS = 1;
