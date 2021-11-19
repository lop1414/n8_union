ALTER TABLE `n8_union`.`tmp_user_login_actions`
    ADD COLUMN `adv_click_id` varchar(500) NULL COMMENT '广告商点击ID' AFTER `oaid`;
