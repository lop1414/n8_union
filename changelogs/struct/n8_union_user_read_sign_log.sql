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

 Date: 24/10/2022 18:26:24
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for n8_union_user_read_sign_log
-- ----------------------------
DROP TABLE IF EXISTS `n8_union_user_read_sign_log`;
CREATE TABLE `n8_union_user_read_sign_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` bigint(20) NOT NULL,
  `read_sign_type` varchar(50) NOT NULL COMMENT '阅读标记枚举',
  `created_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uuid_read_sign_type` (`uuid`,`read_sign_type`) USING BTREE,
  KEY `uuid` (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='阅读标记';

SET FOREIGN_KEY_CHECKS = 1;
