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

 Date: 28/04/2022 16:51:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for product_money_divide
-- ----------------------------
DROP TABLE IF EXISTS `product_money_divide`;
CREATE TABLE `product_money_divide` (
  `product_id` bigint(20) NOT NULL,
  `divide` int(11) NOT NULL DEFAULT '0' COMMENT '分成',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品分成表';

SET FOREIGN_KEY_CHECKS = 1;
