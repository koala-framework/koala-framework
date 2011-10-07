ALTER TABLE `vpc_shop_orders` ADD `canceled` TINYINT NOT NULL ;
ALTER TABLE `vpc_shop_orders` ADD `invoice_date` DATE NULL ;
ALTER TABLE `vpc_shop_orders` ADD number INT NOT NULL ;
UPDATE `vpc_shop_orders` SET number=id;
ALTER TABLE `vpc_shop_orders` CHANGE `package_sent` `shipped` DATE NULL DEFAULT NULL;
ALTER TABLE `vpc_shop_orders` ADD origin ENUM ('internet', 'phone') NOT NULL DEFAULT 'internet';
