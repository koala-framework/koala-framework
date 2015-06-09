CREATE TABLE IF NOT EXISTS `kwc_article_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(50) CHARACTER SET latin1 NOT NULL,
  `firstname` varchar(100) CHARACTER SET latin1 NOT NULL,
  `lastname` varchar(100) CHARACTER SET latin1 NOT NULL,
  `feedback_email` varchar(200) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `kwc_articles` ADD FOREIGN KEY ( `author_id` ) REFERENCES `kwc_article_authors` (
`id`
) ON DELETE CASCADE;
