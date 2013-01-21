CREATE TABLE IF NOT EXISTS `kwc_article_to_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `kwc_article_to_tag` ADD FOREIGN KEY ( `article_id` ) REFERENCES `kwc_articles` (
`id`
) ON DELETE CASCADE ;

ALTER TABLE `kwc_article_to_tag` ADD FOREIGN KEY ( `tag_id` ) REFERENCES `kwc_tags` (
`id`
) ON DELETE CASCADE ;
