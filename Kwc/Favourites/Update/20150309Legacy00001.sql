CREATE TABLE IF NOT EXISTS `kwc_favourites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
