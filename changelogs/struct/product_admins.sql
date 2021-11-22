/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机
 Source Server Type    : MySQL
 Source Server Version : 80026
 Source Host           : localhost:3306
 Source Schema         : n8_union

 Target Server Type    : MySQL
 Target Server Version : 80026
 File Encoding         : 65001

 Date: 22/11/2021 11:23:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for product_admin
-- ----------------------------
DROP TABLE IF EXISTS `product_admin`;
CREATE TABLE `product_admins` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `product_id` varchar(100) NOT NULL DEFAULT '0' COMMENT '产品id',
  `admin_id` int NOT NULL DEFAULT '0' COMMENT '管理员id',
  `status` varchar(50) NOT NULL COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_admin` (`product_id`,`admin_id`) USING BTREE,
  KEY `admin_id` (`admin_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='产品-管理员关联表';

SET FOREIGN_KEY_CHECKS = 1;
