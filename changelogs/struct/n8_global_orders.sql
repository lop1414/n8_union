/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机 192.168.10.10
 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : localhost:3306
 Source Schema         : n8_union

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 01/02/2021 14:59:57
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for n8_global_orders
-- ----------------------------
DROP TABLE IF EXISTS `n8_global_orders`;
CREATE TABLE `n8_global_orders` (
  `n8_goid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `order_id` varchar(64) NOT NULL COMMENT '订单号',
  PRIMARY KEY (`n8_goid`) USING BTREE,
  UNIQUE KEY `order` (`product_id`,`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='n8全局订单表';

SET FOREIGN_KEY_CHECKS = 1;
