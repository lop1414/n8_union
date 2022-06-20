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

 Date: 07/05/2022 10:12:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cp_commissions
-- ----------------------------
DROP TABLE IF EXISTS `cp_commissions`;
CREATE TABLE `cp_commissions` (
  `cp_type` varchar(50) NOT NULL,
  `commission` int(11) NOT NULL COMMENT '分成',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`cp_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台分成表';

SET FOREIGN_KEY_CHECKS = 1;


INSERT INTO `n8_union`.`cp_commissions` (`cp_type`, `commission`, `created_at`, `updated_at`) VALUES ('YW', 0, NULL, NULL);
INSERT INTO `n8_union`.`cp_commissions` (`cp_type`, `commission`, `created_at`, `updated_at`) VALUES ('TW', 0, NULL, NULL);
INSERT INTO `n8_union`.`cp_commissions` (`cp_type`, `commission`, `created_at`, `updated_at`) VALUES ('BM', 0, NULL, NULL);
INSERT INTO `n8_union`.`cp_commissions` (`cp_type`, `commission`, `created_at`, `updated_at`) VALUES ('FQ', 0, NULL, NULL);
INSERT INTO `n8_union`.`cp_commissions` (`cp_type`, `commission`, `created_at`, `updated_at`) VALUES ('ZY', 0, NULL, NULL);
