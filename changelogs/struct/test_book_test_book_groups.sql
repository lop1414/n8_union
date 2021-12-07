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

 Date: 07/12/2021 15:16:32
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for test_book_test_book_groups
-- ----------------------------
DROP TABLE IF EXISTS `test_book_test_book_groups`;
CREATE TABLE `test_book_test_book_groups` (
  `test_book_id` int(11) NOT NULL DEFAULT '0' COMMENT '书籍id',
  `test_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '测试组id',
  UNIQUE KEY `test_book_groups` (`test_book_id`,`test_group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='测试书籍-测试书籍组关联表';

SET FOREIGN_KEY_CHECKS = 1;
