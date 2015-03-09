CREATE TABLE IF NOT EXISTS `kwc_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pos` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `kwc_component_to_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(200) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE  `kwc_component_to_tag` ADD FOREIGN KEY (  `tag_id` ) REFERENCES  `kwc_tags` (
`id`
) ON DELETE CASCADE ;
