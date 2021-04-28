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

 Date: 23/04/2021 12:05:45
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user_shortcut_actions
-- ----------------------------
DROP TABLE IF EXISTS `user_shortcut_actions`;
CREATE TABLE `user_shortcut_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `n8_guid` bigint(20) NOT NULL,
  `uuid` bigint(20) DEFAULT NULL COMMENT '联运用户ID',
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `channel_id` bigint(20) DEFAULT NULL COMMENT '渠道ID',
  `action_time` datetime NOT NULL COMMENT '行为时间',
  `adv_alias` varchar(50) DEFAULT NULL COMMENT '广告商标识',
  `click_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '点击ID',
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
  `last_match_time` datetime DEFAULT NULL COMMENT '最后匹配时间',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `n8_guid` (`n8_guid`,`action_time`) USING BTREE,
  KEY `action_time` (`action_time`),
  KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户加桌行为表';

SET FOREIGN_KEY_CHECKS = 1;
