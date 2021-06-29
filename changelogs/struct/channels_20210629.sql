ALTER TABLE `n8_union`.`channels`
ADD COLUMN `extends` mediumtext NULL COMMENT '扩展信息' AFTER `force_chapter_id`;
