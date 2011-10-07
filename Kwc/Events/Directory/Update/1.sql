CREATE TABLE IF NOT EXISTS `vpc_events` (
  `id` int(11) NOT NULL auto_increment,
  `start_date` datetime NOT NULL,
  `end_date` datetime default NULL,
  `place` varchar(200) collate utf8_unicode_ci NOT NULL,
  `component_id` varchar(200) collate utf8_unicode_ci NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `teaser` text collate utf8_unicode_ci NOT NULL,
  `filename` varchar(50) collate utf8_unicode_ci NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
