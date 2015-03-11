ALTER TABLE  `kwc_shop_orders` CHANGE  `status`  `status` ENUM(  'cart',  'processing',  'ordered',  'payed' ) NOT NULL;
