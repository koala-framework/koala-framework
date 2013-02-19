CREATE TABLE IF NOT EXISTS `kwc_article_to_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `tag_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE  `kwc_article_to_category` ADD FOREIGN KEY (  `article_id` ) REFERENCES  `kwc_articles` (
`id`
) ON DELETE CASCADE ;

ALTER TABLE  `kwc_article_to_category` ADD FOREIGN KEY (  `category_id` ) REFERENCES  `kwc_directories_categories` (
`id`
) ON DELETE CASCADE ;
