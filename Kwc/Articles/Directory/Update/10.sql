CREATE TABLE IF NOT EXISTS `kwc_article_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `kwc_article_views` ADD FOREIGN KEY ( `article_id` ) REFERENCES `kwc_articles` (
`id`
) ON DELETE CASCADE ;
