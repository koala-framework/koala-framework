CREATE TABLE IF NOT EXISTS `kwc_posts_to_categories` (
  `id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `post_id` (`post_id`,`category_id`),
  KEY `post_id_2` (`post_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `kwc_posts_to_categories` ADD FOREIGN KEY ( `post_id` ) REFERENCES `kwc_blog` (`id`);
ALTER TABLE `kwc_posts_to_categories` ADD FOREIGN KEY ( `category_id` ) REFERENCES `kwc_directories_categories` (`id`);

