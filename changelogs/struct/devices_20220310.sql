ALTER TABLE `n8_union`.`devices`
    ADD COLUMN `has_network_license` varchar(50) NULL COMMENT '是否申请备案许可' AFTER `model`;
