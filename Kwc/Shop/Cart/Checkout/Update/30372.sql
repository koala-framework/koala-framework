ALTER TABLE `vpc_shop_orders` ADD `invoice_number` INT NULL ;
UPDATE `vpc_shop_orders` SET shipped = NOW();


