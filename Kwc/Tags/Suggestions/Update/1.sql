CREATE TABLE IF NOT EXISTS `kwc_tag_suggestions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_to_tag_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('new','accepted','denied') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `component_to_tag_id` (`component_to_tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE  `kwc_tag_suggestions` ADD FOREIGN KEY (  `component_to_tag_id` ) REFERENCES  `kwc_component_to_tag` (
`id`
) ON DELETE CASCADE ;
