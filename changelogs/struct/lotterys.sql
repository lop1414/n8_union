/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_union

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-05-27 11:24:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lotterys
-- ----------------------------
DROP TABLE IF EXISTS `lotterys`;
CREATE TABLE `lotterys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `description` text COMMENT '说明',
  `cycle_type` varchar(50) NOT NULL DEFAULT '' COMMENT '周期类型',
  `max_times` int(11) NOT NULL DEFAULT '0' COMMENT '最大次数',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `extends` text COMMENT '扩展字段',
  `release_data` text COMMENT '发布数据',
  `release_at` timestamp NULL DEFAULT NULL COMMENT '发布时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='抽奖活动表';
