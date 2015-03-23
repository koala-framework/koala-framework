CREATE TABLE IF NOT EXISTS `paypal_ipn_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `test_ipn` tinyint(4) NOT NULL,
  `payer_id` varchar(200) NOT NULL,
  `payer_email` varchar(200) NOT NULL,
  `data` text NOT NULL,
  `txn_type` varchar(100) NOT NULL,
  `custom` varchar(255) NOT NULL,
  `item_number` varchar(200) NOT NULL,
  `callback_success` TINYINT NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `custom` (`custom`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;