ALTER TABLE `vpc_shop_orders` ADD `invoice_number` INT NULL ;
ALTER TABLE `vpc_shop_orders` SET shipped = NOW();


