CREATE TABLE IF NOT EXISTS `kwc_article_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pos` smallint(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `kwc_article_categories` ADD FOREIGN KEY ( `parent_id` ) REFERENCES `kwc_article_categories` (
`id`
) ON UPDATE CASCADE;
