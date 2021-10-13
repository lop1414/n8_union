ALTER TABLE `n8_union`.`n8_union_users`
ADD COLUMN `user_type` varchar(50) NOT NULL DEFAULT '' COMMENT '用户类型' AFTER `last_match_time`;
