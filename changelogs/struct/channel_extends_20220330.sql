ALTER TABLE `n8_union`.`channel_extends`
    ADD COLUMN `parent_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '父渠道ID' AFTER `admin_id`;
