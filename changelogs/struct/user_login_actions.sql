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

 Date: 23/03/2021 16:03:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user_login_actions
-- ----------------------------
DROP TABLE IF EXISTS `user_login_actions`;
CREATE TABLE `user_login_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `n8_guid` varchar(64) NOT NULL,
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `action_time` datetime NOT NULL COMMENT '行为时间',
  `ip` varchar(50) DEFAULT NULL,
  `ua` text,
  `muid` varchar(255) DEFAULT NULL COMMENT '设备号',
  `oaid` varchar(255) DEFAULT NULL,
  `device_brand` varchar(255) DEFAULT NULL COMMENT '设备品牌',
  `device_manufacturer` varchar(255) DEFAULT NULL COMMENT '设备生产商',
  `device_model` varchar(255) DEFAULT NULL COMMENT '设备型号',
  `device_product` varchar(255) DEFAULT NULL COMMENT '设备代号',
  `device_os_version_name` varchar(255) DEFAULT NULL COMMENT '操作系统版本名称',
  `device_os_version_code` varchar(255) DEFAULT NULL COMMENT '操作系统版本号',
  `device_platform_version_name` varchar(255) DEFAULT NULL COMMENT '运行平台版本名称',
  `device_platform_version_code` varchar(255) DEFAULT NULL COMMENT '运行平台版本号',
  `android_id` varchar(255) DEFAULT NULL COMMENT '安卓ID',
  `request_id` varchar(255) DEFAULT NULL COMMENT '广告商的请求ID',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `n8_guid` (`n8_guid`,`action_time`) USING BTREE,
  KEY `action_time` (`action_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户登陆行为表';

SET FOREIGN_KEY_CHECKS = 1;
