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

 Date: 10/06/2022 14:58:28
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for product_weixin_mini_programs
-- ----------------------------
DROP TABLE IF EXISTS `product_weixin_mini_programs`;
CREATE TABLE `product_weixin_mini_programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `weixin_mini_program_id` int(11) NOT NULL COMMENT '微信小程序id',
  `url` varchar(255) NOT NULL COMMENT '引导页url',
  `path` varchar(255) NOT NULL COMMENT '小程序引导路径',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `product_id` (`product_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品-小程序引导关注配置';

SET FOREIGN_KEY_CHECKS = 1;
