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

 Date: 07/07/2022 09:55:05
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for book_book_labels
-- ----------------------------
DROP TABLE IF EXISTS `book_book_labels`;
CREATE TABLE `book_book_labels` (
  `book_id` bigint(20) NOT NULL,
  `book_label_id` bigint(20) NOT NULL COMMENT '书籍标签ID',
  UNIQUE KEY `book_book_label` (`book_id`,`book_label_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书籍-书籍标签表';

SET FOREIGN_KEY_CHECKS = 1;
