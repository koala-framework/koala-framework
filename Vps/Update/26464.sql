DROP TABLE IF EXISTS `cache_component_meta`;
CREATE TABLE IF NOT EXISTS `cache_component_meta` (
  `model` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `field` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `value` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `type` enum('cacheId','callback','componentClass') character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`model`,`id`,`value`,`type`,`field`(1)),
  KEY `model` (`model`),
  KEY `id` (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
