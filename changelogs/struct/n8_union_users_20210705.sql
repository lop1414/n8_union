ALTER TABLE `n8_union`.`n8_union_users`
ADD COLUMN `platform` varchar(50) NOT NULL DEFAULT '' COMMENT '平台' AFTER `force_chapter_id`;
