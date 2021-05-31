/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_union

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-05-31 11:04:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lottery_prize_logs
-- ----------------------------
DROP TABLE IF EXISTS `lottery_prize_logs`;
CREATE TABLE `lottery_prize_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `n8_guid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'n8全局用户id',
  `lottery_id` int(11) NOT NULL DEFAULT '0' COMMENT '抽奖id',
  `prize_id` int(11) NOT NULL DEFAULT '0' COMMENT '奖品id',
  `prize_type` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品类型',
  `exchange_status` varchar(50) NOT NULL DEFAULT '' COMMENT '兑换状态',
  `extends` text COMMENT '扩展字段',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `n8_guid` (`n8_guid`) USING BTREE,
  KEY `prize_id` (`prize_id`) USING BTREE,
  KEY `lottery_id` (`lottery_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='抽奖获奖日志表';
