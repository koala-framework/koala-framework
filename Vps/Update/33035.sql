#tags: vpc
DROP TABLE IF EXISTS `cache_component_meta_component`;
CREATE TABLE IF NOT EXISTS `cache_component_meta_component` (
  `db_id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL default '',
  `component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `target_db_id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `target_component_class` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `meta_class` varchar(255) NOT NULL,
  PRIMARY KEY  (`db_id`,`component_class`,`target_db_id`,`target_component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
