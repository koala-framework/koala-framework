CREATE TABLE IF NOT EXISTS `kwf_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos` tinyint(4) NOT NULL,
  `parent_id` varchar(255) NOT NULL,
  `is_home` tinyint(4) NOT NULL,
  `filename` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `hide` tinyint(4) NOT NULL,
  `component` varchar(50) NOT NULL,
  `custom_filename` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
