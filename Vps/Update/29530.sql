#tags: vps

CREATE TABLE IF NOT EXISTS `cache_users` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(255) NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  `password` varchar(40) NOT NULL,
  `password_salt` varchar(10) NOT NULL,
  `gender` enum('','female','male') NOT NULL,
  `title` varchar(100) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `webcode` varchar(100) NOT NULL,
  `created` datetime default NULL,
  `logins` int(11) default NULL,
  `last_login` datetime default NULL,
  `last_modified` datetime NOT NULL,
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `email` (`email`),
  KEY `webcode` (`webcode`),
  KEY `last_modified` (`last_modified`),
  KEY `email_2` (`email`,`webcode`,`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2626 ;

ALTER TABLE `cache_users` ADD INDEX ( `deleted` );
ALTER TABLE `cache_users` ADD INDEX ( `locked` );
ALTER TABLE `cache_users` DROP INDEX `email_2`;
