 CREATE TABLE `kwc_shop_vouchers` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`code` VARCHAR( 20 ) NOT NULL ,
`amount` DECIMAL( 10, 2 ) NOT NULL ,
`date` DATETIME NOT NULL ,
`comment` TEXT NOT NULL
) ENGINE = InnoDB;

ALTER TABLE `kwc_shop_vouchers` ADD UNIQUE (`code`) ;
 ALTER TABLE `kwc_shop_vouchers` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT  ;

CREATE TABLE `kwc_shop_voucher_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `voucher_id` INT UNSIGNED NOT NULL ,
    `order_id` INT UNSIGNED NULL ,
    `amount` DECIMAL( 10, 2 ) NOT NULL ,
INDEX ( `voucher_id` )
) ENGINE = InnoDB ;
 ALTER TABLE `kwc_shop_voucher_history` ADD INDEX ( `order_id` )  ;
ALTER TABLE `kwc_shop_voucher_history` ADD `date` DATETIME NOT NULL ,
ADD `comment` TEXT NOT NULL ;
 ALTER TABLE `kwc_shop_voucher_history` ADD FOREIGN KEY ( `voucher_id` ) REFERENCES `kwc_shop_vouchers` (
`id`
);

ALTER TABLE `kwc_shop_voucher_history` ADD FOREIGN KEY ( `order_id` ) REFERENCES `kwc_shop_orders` (
`id`
);

 ALTER TABLE `kwc_shop_voucher_history` CHANGE `order_id` `order_id` INT( 10 ) UNSIGNED NULL  ;
 