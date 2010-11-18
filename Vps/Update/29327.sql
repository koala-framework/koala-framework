#tags: vpc
DROP TABLE IF EXISTS `cache_component`;
CREATE TABLE IF NOT EXISTS `cache_component` (
  `id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `page_id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `component_class` varchar(100) character set latin1 collate latin1_general_ci NOT NULL,
  `content` longtext character set utf8 NOT NULL,
  `last_modified` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_class` (`component_class`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM ;
