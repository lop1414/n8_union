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

 Date: 14/03/2022 10:56:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ua_device_custom_brands
-- ----------------------------
DROP TABLE IF EXISTS `ua_device_custom_brands`;
CREATE TABLE `ua_device_custom_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(50) NOT NULL COMMENT '型号',
  `brand` varchar(50) DEFAULT NULL COMMENT '品牌',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `model` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备自定义品牌';

SET FOREIGN_KEY_CHECKS = 1;
