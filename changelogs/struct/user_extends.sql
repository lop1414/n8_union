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

 Date: 03/06/2021 17:24:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user_extends
-- ----------------------------
DROP TABLE IF EXISTS `user_extends`;
CREATE TABLE `user_extends` (
  `n8_guid` bigint(20) NOT NULL,
  `ip` varchar(15) DEFAULT NULL COMMENT 'IP',
  `ua` text COMMENT 'UA',
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
  PRIMARY KEY (`n8_guid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户扩展信息表';

SET FOREIGN_KEY_CHECKS = 1;
