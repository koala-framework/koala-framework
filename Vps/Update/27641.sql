#tags: vps 
CREATE TABLE `cache_component_fields` (
  `model` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `field` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`model`,`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `cache_component_meta` ADD INDEX ( `field` );