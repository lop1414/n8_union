ALTER TABLE `n8_union`.`chapters`
MODIFY COLUMN `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称' AFTER `cp_chapter_id`;
