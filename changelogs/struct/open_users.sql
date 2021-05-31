/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_union

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-05-27 11:24:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for open_users
-- ----------------------------
DROP TABLE IF EXISTS `open_users`;
CREATE TABLE `open_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_source` varchar(50) NOT NULL DEFAULT '' COMMENT '用户来源',
  `source_app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '来源app_id',
  `source_open_id` varchar(64) NOT NULL DEFAULT '' COMMENT '来源open_id',
  `n8_guid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'n8全局用户id',
  `extends` text COMMENT '扩展字段',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_open_id` (`user_source`,`source_app_id`,`source_open_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='第三方用户表';
