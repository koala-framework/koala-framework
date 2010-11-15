#tags: vpc
DROP TABLE IF EXISTS 
`cache_component` ,
`cache_component_fields` ,
`cache_component_meta` ,
`cache_componentpreload` ,
`cache_component_meta_chained` ,
`cache_component_meta_component` ,
`cache_component_meta_model` ,
`cache_component_meta_row` ;

CREATE TABLE IF NOT EXISTS `cache_component` (
  `component_id` varchar(255) collate latin1_general_ci NOT NULL,
  `page_id` varchar(255) collate latin1_general_ci default NULL,
  `db_id` varchar(255) collate latin1_general_ci default NULL,
  `component_class` varchar(255) collate latin1_general_ci NOT NULL,
  `type` enum('component','master','partials','partial','mail','componentLink') collate latin1_general_ci NOT NULL,
  `value` varchar(20) collate latin1_general_ci NOT NULL default '' COMMENT 'Bei Partial partialId oder bei master component_id zu der das master gehört',
  `expire` int(11) default NULL,
  `deleted` smallint(1) NOT NULL default '0',
  `content` longtext character set utf8 NOT NULL,
  PRIMARY KEY  (`component_id`,`type`,`value`),
  KEY `page_id` (`page_id`),
  KEY `component_class` (`component_class`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cache_componentpreload` (
  `page_id` varchar(255) collate latin1_general_ci NOT NULL,
  `preload_component_id` varchar(255) collate latin1_general_ci NOT NULL,
  `preload_type` varchar(20) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`page_id`,`preload_component_id`,`preload_type`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cache_component_meta_chained` (
  `source_component_class` varchar(255) collate latin1_general_ci NOT NULL,
  `target_component_class` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`source_component_class`,`target_component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache_component_meta_component` (
  `component_id` varchar(255) collate latin1_general_ci NOT NULL,
  `component_class` varchar(255) collate latin1_general_ci NOT NULL,
  `target_component_id` varchar(255) collate latin1_general_ci NOT NULL,
  `target_component_class` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`component_id`,`target_component_id`),
  KEY `source_component_id` (`target_component_id`),
  KEY `source_component_class` (`target_component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cache_component_meta_model` (
  `model` varchar(255) collate latin1_general_ci NOT NULL,
  `component_class` varchar(255) collate latin1_general_ci NOT NULL,
  `pattern` varchar(100) collate latin1_general_ci NOT NULL default '',
  `meta_class` varchar(100) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`model`,`component_class`,`pattern`,`meta_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `cache_component_meta_row` (
  `model` varchar(255) collate latin1_general_ci NOT NULL,
  `column` varchar(60) collate latin1_general_ci NOT NULL,
  `value` varchar(100) collate latin1_general_ci NOT NULL,
  `component_id` varchar(255) collate latin1_general_ci NOT NULL,
  `component_class` varchar(255) collate latin1_general_ci NOT NULL,
  `meta_class` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`model`,`column`,`value`,`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
