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

 Date: 07/05/2022 10:12:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cp_commission_logs
-- ----------------------------
DROP TABLE IF EXISTS `cp_commission_logs`;
CREATE TABLE `cp_commission_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cp_type` varchar(50) NOT NULL COMMENT '平台类型',
  `commission` int(11) NOT NULL COMMENT '分成',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `cp_type` (`cp_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台分成表日志表';

SET FOREIGN_KEY_CHECKS = 1;
