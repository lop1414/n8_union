/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机 192.168.10.10
 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : localhost:3306
 Source Schema         : n8_product_kyy

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 23/03/2021 16:03:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user_info_change_logs
-- ----------------------------
DROP TABLE IF EXISTS `user_info_change_logs`;
CREATE TABLE `user_info_change_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `n8_guid` bigint(20) NOT NULL,
  `field` varchar(60) NOT NULL COMMENT '更新字段',
  `change_before` varchar(512) NOT NULL COMMENT '更新前',
  `change_after` varchar(512) NOT NULL COMMENT '更新后',
  `change_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user` (`n8_guid`) USING BTREE,
  KEY `change_time` (`change_time`),
  KEY `field` (`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息更改日志表';

SET FOREIGN_KEY_CHECKS = 1;
