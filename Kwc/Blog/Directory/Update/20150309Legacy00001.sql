CREATE TABLE IF NOT EXISTS `kwc_blog_posts` (
  `id` int(11) NOT NULL auto_increment,
  `component_id` varchar(255) collate utf8_unicode_ci NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `publish_date` date NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `kwc_blog_posts` ADD `author_id` INT NOT NULL AFTER `component_id`;
ALTER TABLE `kwc_blog_posts` ADD INDEX ( `author_id` );

