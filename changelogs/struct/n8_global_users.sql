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

 Date: 01/02/2021 15:00:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for n8_global_users
-- ----------------------------
DROP TABLE IF EXISTS `n8_global_users`;
CREATE TABLE `n8_global_users` (
  `n8_guid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `open_id` varchar(64) NOT NULL COMMENT '平台用户ID',
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  PRIMARY KEY (`n8_guid`) USING BTREE,
  UNIQUE KEY `user` (`product_id`,`open_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='n8全局用户表';

SET FOREIGN_KEY_CHECKS = 1;
