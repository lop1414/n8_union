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

 Date: 06/03/2021 18:10:56
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cp_type` varchar(50) NOT NULL COMMENT '平台类型',
  `cp_book_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '平台书籍ID',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `author_name` varchar(50) NOT NULL COMMENT '作者',
  `all_words` int(11) NOT NULL DEFAULT '0' COMMENT '总字数',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cp_book_id` (`cp_type`,`cp_book_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书籍表';

SET FOREIGN_KEY_CHECKS = 1;
