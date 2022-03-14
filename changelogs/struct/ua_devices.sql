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

 Date: 12/03/2022 15:30:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ua_devices
-- ----------------------------
DROP TABLE IF EXISTS `ua_devices`;
CREATE TABLE `ua_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(50) DEFAULT NULL COMMENT '型号',
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `brand` varchar(50) DEFAULT NULL COMMENT '品牌',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `model` (`model`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ua设备表';

SET FOREIGN_KEY_CHECKS = 1;
