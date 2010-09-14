ALTER TABLE `vpc_shop_order_products` ADD `data` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;
 ALTER TABLE `vpc_shop_order_products` CHANGE `size` `size_backup` TINYINT( 4 ) NOT NULL;
  ALTER TABLE `vpc_shop_order_products` CHANGE `amount` `amount_backup` SMALLINT NOT NULL  ;
  