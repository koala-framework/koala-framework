CREATE TABLE IF NOT EXISTS `kwc_articles_trl` (
  `component_id` varchar(200) NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `teaser` text COLLATE utf8_unicode_ci NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`component_id`),
  KEY `visible` (`visible`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
