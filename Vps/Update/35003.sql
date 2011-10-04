#tags: vpc

DROP TABLE IF EXISTS `cache_component` ,
`cache_component_fields` ,
`cache_component_meta` ,
`cache_component_meta_chained` ,
`cache_component_meta_component` ,
`cache_component_meta_model` ,
`cache_component_meta_row` ,
`cache_component_url` ,
`cache_component_url_parents` ;

CREATE TABLE IF NOT EXISTS `cache_component` (
  `component_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `db_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type` enum('page','component','master','partials','partial','mail','componentLink') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Bei Partial partialId oder bei master component_id zu der das master gehÃƒÂ¶rt',
  `expire` int(11) DEFAULT NULL,
  `deleted` smallint(1) NOT NULL DEFAULT '0',
  `content` longtext NOT NULL,
  PRIMARY KEY (`component_id`,`type`,`value`),
  KEY `component_class` (`component_class`),
  KEY `db_id` (`db_id`),
  KEY `value` (`value`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache_component_meta_chained` (
  `source_component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `target_component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`source_component_class`,`target_component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache_component_meta_component` (
  `db_id` varchar(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `target_db_id` varchar(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `target_component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `meta_class` varchar(255) NOT NULL,
  PRIMARY KEY (`db_id`,`component_class`,`target_db_id`,`target_component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache_component_meta_model` (
  `model` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `pattern` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `meta_class` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`model`,`component_class`,`pattern`,`meta_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache_component_meta_row` (
  `model` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `column` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `component_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `meta_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`model`,`column`,`value`,`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache_component_url` (
  `url` varchar(255) NOT NULL,
  `page_id` varchar(200) NOT NULL,
  PRIMARY KEY (`url`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cache_component_url_parents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` varchar(200) NOT NULL,
  `parent_page_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `parent_page_id` (`parent_page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=310129 ;
