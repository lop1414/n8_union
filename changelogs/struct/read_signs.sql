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

 Date: 25/10/2022 17:34:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for read_signs
-- ----------------------------
DROP TABLE IF EXISTS `read_signs`;
CREATE TABLE `read_signs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `book_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '书籍id',
  `sign_chapter_id_1` bigint(20) NOT NULL DEFAULT '0' COMMENT '标记章节id1',
  `sign_chapter_id_2` bigint(20) NOT NULL DEFAULT '0' COMMENT '标记章节id2',
  `sign_chapter_id_3` bigint(20) NOT NULL DEFAULT '0' COMMENT '标记章节id3',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `read_sign` (`book_id`,`sign_chapter_id_1`,`sign_chapter_id_2`,`sign_chapter_id_3`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='阅读标记表';

SET FOREIGN_KEY_CHECKS = 1;
