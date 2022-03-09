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

 Date: 09/03/2022 17:27:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for device_network_license
-- ----------------------------
DROP TABLE IF EXISTS `device_network_license`;
CREATE TABLE `device_network_license` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `model` varchar(50) DEFAULT NULL COMMENT '型号',
  `apply_org` varchar(50) DEFAULT NULL COMMENT '申请单位',
  `reg_date` date DEFAULT NULL COMMENT '发证日期',
  `end_date` date DEFAULT NULL COMMENT '有效日期',
  `license_no` varchar(50) DEFAULT NULL COMMENT '进网证号',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `model_org` (`model`,`apply_org`,`license_no`,`reg_date`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备网络许可申请表';

SET FOREIGN_KEY_CHECKS = 1;
