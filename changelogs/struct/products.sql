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

 Date: 20/02/2021 19:02:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `cp_account_id` int(11) NOT NULL COMMENT '平台账户ID',
  `cp_product_alias` varchar(50) NOT NULL COMMENT '平台产品标识',
  `cp_secret` varchar(255) DEFAULT NULL COMMENT '平台密钥',
  `cp_type` varchar(50) NOT NULL COMMENT '平台类型',
  `type` varchar(50) NOT NULL COMMENT '产品类型',
  `secret` varchar(255) NOT NULL COMMENT '密钥',
  `keyword` varchar(50) DEFAULT NULL COMMENT '关键字',
  `logo` varchar(255) DEFAULT NULL COMMENT '图标',
  `status` varchar(50) NOT NULL COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `cp_product_alias` (`cp_product_alias`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COMMENT='产品';

SET FOREIGN_KEY_CHECKS = 1;
