ALTER TABLE `n8_union`.`channels`
    ADD COLUMN `read_sign_id` bigint(20) NOT NULL COMMENT '阅读标记id' AFTER `force_chapter_id`;
