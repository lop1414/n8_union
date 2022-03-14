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

 Date: 12/03/2022 15:30:08
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ua_device_logs
-- ----------------------------
DROP TABLE IF EXISTS `ua_device_logs`;
CREATE TABLE `ua_device_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ua_device_id` varchar(50) NOT NULL COMMENT '设备id',
  `model` varchar(50) DEFAULT NULL COMMENT '型号',
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `brand` varchar(50) DEFAULT NULL COMMENT '品牌',
  `extends` text COMMENT '扩展信息',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ua设备表日志表';

SET FOREIGN_KEY_CHECKS = 1;
