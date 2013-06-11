CREATE TABLE IF NOT EXISTS `kwc_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `from` datetime NOT NULL,
  `to` datetime DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14;
