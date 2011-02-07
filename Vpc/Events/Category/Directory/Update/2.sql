CREATE TABLE IF NOT EXISTS `vpc_events_to_categories` (
  `id` int(11) NOT NULL auto_increment,
  `event_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `event_id` (`event_id`,`category_id`),
  KEY `event_id_2` (`event_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
