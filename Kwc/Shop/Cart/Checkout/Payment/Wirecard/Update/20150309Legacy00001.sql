CREATE TABLE IF NOT EXISTS `kwc_wirecard_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` text NOT NULL,
  `custom_order_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_order_id` (`custom_order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
