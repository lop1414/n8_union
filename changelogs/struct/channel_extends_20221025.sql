ALTER TABLE `n8_union`.`channel_extends`
    ADD COLUMN `read_sign_id` bigint(20) NOT NULL COMMENT '阅读标记id' AFTER `parent_id`;
