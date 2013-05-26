CREATE TABLE IF NOT EXISTS `kwc_tag_suggestions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tags_to_components_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('new','accepted','denied') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `tags_to_components_id` (`tags_to_components_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE  `kwc_tag_suggestions` ADD FOREIGN KEY (  `tags_to_components_id` ) REFERENCES  `kwc_tags_to_components` (
`id`
) ON DELETE CASCADE ;
