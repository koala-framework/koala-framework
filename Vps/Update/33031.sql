#tags: vpc
DROP TABLE IF EXISTS `cache_component`;
CREATE TABLE IF NOT EXISTS `cache_component` (
  `id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `page_id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `content` longtext character set utf8 NOT NULL,
  `last_modified` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_class` (`component_class`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
