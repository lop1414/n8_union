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

 Date: 08/12/2021 15:12:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for test_book_group_admin_users
-- ----------------------------
DROP TABLE IF EXISTS `test_book_group_admin_users`;
CREATE TABLE `test_book_group_admin_users` (
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `test_book_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '测试组id',
  UNIQUE KEY `test_book_group_admin_users` (`admin_id`,`test_book_group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员-测试书籍组关联表';

SET FOREIGN_KEY_CHECKS = 1;
