ALTER TABLE `kwc_shop_orders` CHANGE `status` `status` ENUM( 'cart', 'ordered', 'payed' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
