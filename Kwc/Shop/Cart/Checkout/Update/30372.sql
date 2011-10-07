ALTER TABLE `kwc_shop_orders` ADD `invoice_number` INT NULL ;
UPDATE `kwc_shop_orders` SET shipped = NOW();


