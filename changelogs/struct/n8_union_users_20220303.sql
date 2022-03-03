ALTER TABLE `n8_union`.`n8_union_users`
ADD COLUMN `brand` varchar(50) NOT NULL DEFAULT '' COMMENT '设备品牌' AFTER `user_type`;
