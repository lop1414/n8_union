ALTER TABLE `n8_union`.`n8_union_users`
DROP COLUMN `device_id`,
ADD COLUMN `device_model` varchar(50) NOT NULL DEFAULT '' COMMENT '设备型号' AFTER `sys_version`;
