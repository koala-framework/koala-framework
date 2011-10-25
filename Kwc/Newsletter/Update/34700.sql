

CREATE TABLE IF NOT EXISTS `kwc_newsletter_subscribers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `gender` enum('','female','male') NOT NULL,
  `title` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `format` enum('','html','text') NOT NULL,
  `subscribe_date` datetime NOT NULL,
  `unsubscribed` tinyint(1) NOT NULL,
  `activated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `kwc_newsletter_subscribers_to_pool` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `subscriber_id` int(10) unsigned NOT NULL,
  `pool_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `pool_id` (`pool_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

