CREATE TABLE IF NOT EXISTS `kwc_paragraphs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `pos` smallint(6) NOT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '0',
  `component` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
