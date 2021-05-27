/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : n8_union

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-05-27 11:24:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lottery_prizes
-- ----------------------------
DROP TABLE IF EXISTS `lottery_prizes`;
CREATE TABLE `lottery_prizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lottery_id` int(11) NOT NULL DEFAULT '0' COMMENT '抽奖id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `prize_type` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品类型',
  `chance` int(11) NOT NULL DEFAULT '0' COMMENT '概率',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '总量',
  `image_url` varchar(512) NOT NULL DEFAULT '' COMMENT '图片地址',
  `extends` text COMMENT '扩展字段',
  `status` varchar(50) NOT NULL DEFAULT '' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `lottery_id` (`lottery_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='抽奖奖品表';
