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

 Date: 11/03/2022 17:57:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for device_names
-- ----------------------------
DROP TABLE IF EXISTS `device_names`;
CREATE TABLE `device_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(50) NOT NULL COMMENT '设备型号',
  `name` varchar(50) DEFAULT NULL COMMENT '设备名称',
  `version` varchar(50) DEFAULT NULL COMMENT '版本',
  `source` varchar(50) DEFAULT NULL COMMENT '来源',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `model_version` (`version`,`model`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='设备名称表';

SET FOREIGN_KEY_CHECKS = 1;
