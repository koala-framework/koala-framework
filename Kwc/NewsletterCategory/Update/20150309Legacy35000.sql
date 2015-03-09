CREATE TABLE IF NOT EXISTS `kwc_newsletter_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `component_id` varchar(255) NOT NULL,
  `pos` smallint(6) NOT NULL,
  `category` varchar(255) NOT NULL,
  `kwf_pool_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;