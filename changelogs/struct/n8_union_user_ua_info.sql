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

 Date: 12/03/2022 16:56:37
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for n8_union_user_ua_info
-- ----------------------------
DROP TABLE IF EXISTS `n8_union_user_ua_info`;
CREATE TABLE `n8_union_user_ua_info` (
  `uuid` bigint(20) NOT NULL,
  `ua_device_id` varchar(50) NOT NULL COMMENT '设备id',
  `sys_version` varchar(50) DEFAULT NULL COMMENT '系统版本',
  PRIMARY KEY (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户ua信息表';

SET FOREIGN_KEY_CHECKS = 1;
