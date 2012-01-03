CREATE TABLE IF NOT EXISTS `kwc_shop_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('cart','ordered','payed') NOT NULL,
  `ip` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_component_id` varchar(200) NOT NULL,
  `checkout_component_id` varchar(200) NOT NULL,
  `cart_component_class` varchar(200) NOT NULL,
  `data` text NOT NULL,
  `shipped` date DEFAULT NULL,
  `payed` date DEFAULT NULL,
  `canceled` tinyint(4) NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `number` int(11) NOT NULL,
  `origin` enum('internet','phone','folder','fair') NOT NULL DEFAULT 'internet',
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `zip` varchar(50) NOT NULL,
  `payment` varchar(100) NOT NULL,
  `invoice_number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `kwc_shop_order_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_order_id` int(10) unsigned NOT NULL,
  `shop_product_price_id` int(10) unsigned DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `pos` smallint(6) NOT NULL,
  `add_component_id` varchar(200) NOT NULL,
  `add_component_class` varchar(200) NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_order_id` (`shop_order_id`),
  KEY `shop_product_price_id` (`shop_product_price_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `kwc_shop_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `pos` smallint(6) NOT NULL,
  `title` varchar(200) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `max_amount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `kwc_shop_product_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_product_id` int(10) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `valid_from` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_product_id` (`shop_product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

ALTER TABLE `kwc_shop_order_products`
  ADD CONSTRAINT `kwc_shop_order_products_ibfk_1` FOREIGN KEY (`shop_order_id`) REFERENCES `kwc_shop_orders` (`id`),
  ADD CONSTRAINT `kwc_shop_order_products_ibfk_2` FOREIGN KEY (`shop_product_price_id`) REFERENCES `kwc_shop_product_prices` (`id`);

ALTER TABLE `kwc_shop_product_prices`
  ADD CONSTRAINT `kwc_shop_product_prices_ibfk_1` FOREIGN KEY (`shop_product_id`) REFERENCES `kwc_shop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `kwc_shop_order_products` CHANGE `shop_product_price_id` `shop_product_price_id` INT( 10 ) UNSIGNED NULL;
