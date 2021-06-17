ALTER TABLE `n8_union`.`orders`
DROP INDEX `n8_guid`,
ADD INDEX `n8_guid`(`n8_guid`) USING BTREE;
