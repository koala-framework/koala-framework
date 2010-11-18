CREATE TABLE IF NOT EXISTS `vpc_posts` (
  `id` int(11) NOT NULL auto_increment,
  `component_id` varchar(255) NOT NULL,
  `visible` tinyint(1) NOT NULL default '1',
  `create_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned default NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_id` (`component_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
