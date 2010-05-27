CREATE TABLE IF NOT EXISTS `vpc_news_to_categories` (
  `id` int(11) NOT NULL auto_increment,
  `news_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `news_id` (`news_id`,`category_id`),
  KEY `news_id_2` (`news_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

