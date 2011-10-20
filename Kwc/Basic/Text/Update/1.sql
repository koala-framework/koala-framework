CREATE TABLE IF NOT EXISTS `kwc_basic_text` (
  `component_id` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `content_edit` text,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kwc_basic_text_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(200) NOT NULL,
  `component` enum('image','link','download') NOT NULL,
  `nr` int(10) unsigned NOT NULL,
  `saved` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `kwc_basic_text_styles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `pos` int(10) unsigned NOT NULL,
  `tag` varchar(20) NOT NULL,
  `ownStyles` varchar(30) NOT NULL,
  `master` tinyint(4) NOT NULL,
  `styles` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
