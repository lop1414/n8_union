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

 Date: 07/03/2022 17:43:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for devices
-- ----------------------------
DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(50) DEFAULT NULL COMMENT '设备名称',
   `brand` varchar(50) DEFAULT NULL COMMENT '设备品牌',
   `model` varchar(50) DEFAULT NULL COMMENT '设备型号',
   PRIMARY KEY (`id`) USING BTREE,
   UNIQUE KEY `brand_model` (`brand`,`model`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备表';

SET FOREIGN_KEY_CHECKS = 1;
