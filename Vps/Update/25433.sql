DROP TABLE IF EXISTS `cache_component_meta`;
CREATE TABLE IF NOT EXISTS `cache_component_meta` (
  `model` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `field` enum('primary','component_id') character set latin1 collate latin1_general_ci NOT NULL default 'primary',
  `value` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `type` enum('cacheId','callback','componentClass','static') character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`model`,`id`,`value`,`type`,`field`),
  KEY `model` (`model`),
  KEY `id` (`id`)
) ENGINE=MyISAM;
