CREATE TABLE IF NOT EXISTS `kwc_composite_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `pos` int(11) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
