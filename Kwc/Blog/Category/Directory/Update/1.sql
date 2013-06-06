CREATE TABLE IF NOT EXISTS `kwc_blog_posts_to_categories` (
  `id` int(11) NOT NULL auto_increment,
  `blog_post_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `blog_post_id` (`blog_post_id`,`category_id`),
  KEY `blog_post_id_2` (`blog_post_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `kwc_blog_posts_to_categories` ADD FOREIGN KEY ( `blog_post_id` ) REFERENCES `kwc_blog_posts` (`id`);
ALTER TABLE `kwc_blog_posts_to_categories` ADD FOREIGN KEY ( `category_id` ) REFERENCES `kwc_directories_categories` (`id`);

