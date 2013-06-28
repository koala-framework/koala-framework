
CREATE TABLE IF NOT EXISTS `kwf_enquiries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `save_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `is_spam` tinyint(1) NOT NULL default '0',
  `mail_sent` tinyint(1) NOT NULL default '0',
  `serialized_mail_vars` text,
  `serialized_mail_essentials` text,
  `mail_attachments` text NOT NULL,
  `sent_mail_content_text` text NOT NULL,
  `sent_mail_content_html` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

# user - sonst geht garnix
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
  KEY `deleted` (`deleted`),
  KEY `locked` (`locked`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kwf_users` (
  `id` int(11) NOT NULL auto_increment,
  `role` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kwf_user_messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `create_type` enum('auto','manual') NOT NULL,
  `message_type` varchar(255) NOT NULL,
  `message_date` datetime NOT NULL,
  `ip` varchar(255) NOT NULL,
  `by_user_id` int(10) unsigned default NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
