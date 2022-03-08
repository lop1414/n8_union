ALTER TABLE `n8_union`.`n8_union_users`
CHANGE COLUMN `brand` `sys_version` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '系统版本' AFTER `user_type`,
ADD COLUMN `device_id` int(11) NULL COMMENT '设备ID' AFTER `sys_version`;
