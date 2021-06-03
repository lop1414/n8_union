ALTER TABLE `lottery_prizes`
ADD COLUMN `order`  int NOT NULL DEFAULT 0 COMMENT '排序值' AFTER `status`;

