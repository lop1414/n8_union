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

 Date: 03/02/2021 15:16:54
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for menu_level
-- ----------------------------
DROP TABLE IF EXISTS `menu_level`;
CREATE TABLE `menu_level` (
  `menu_id` int(11) NOT NULL,
  `level` varchar(50) NOT NULL COMMENT '级别',
  `status` varchar(50) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `menu_id_level` (`menu_id`,`level`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单业务级别关联表';

SET FOREIGN_KEY_CHECKS = 1;
