/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机 192.168.10.10
 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : localhost:3306
 Source Schema         : n8_product_kyy

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 23/03/2021 16:04:05
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `n8_guid` bigint(20) unsigned NOT NULL,
  `product_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `reg_time` datetime NOT NULL COMMENT '注册时间',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`n8_guid`) USING BTREE,
  KEY `reg_time` (`reg_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

SET FOREIGN_KEY_CHECKS = 1;
