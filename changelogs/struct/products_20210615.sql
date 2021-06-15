ALTER TABLE `n8_union`.`products`
ADD COLUMN `operator` varchar(50) NOT NULL COMMENT '运营方' AFTER `matcher`,
ADD COLUMN `extends` text NULL AFTER `operator`;
