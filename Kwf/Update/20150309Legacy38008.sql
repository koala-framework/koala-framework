#tags: kwc
ALTER TABLE  `cache_component` CHANGE  `type`  `type` ENUM(  'page',  'component',  'master',  'partial',  'componentLink',  'fullPage' ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `cache_component_includes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `target_id` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source` (`component_id`,`type`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM ;
