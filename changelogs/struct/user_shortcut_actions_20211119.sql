ALTER TABLE `n8_union`.`user_shortcut_actions`
    ADD COLUMN `adv_click_id` varchar(500) NULL COMMENT '广告商点击ID' AFTER `oaid`;
