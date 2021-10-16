CREATE TABLE IF NOT EXISTS `kwc_mail_redirect` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;
ALTER TABLE `kwc_mail_redirect` ADD INDEX ( `value` );

CREATE TABLE IF NOT EXISTS `kwc_mail_redirect_statistics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `mail_component_id` varchar(255) NOT NULL,
  `redirect_id` int(10) unsigned default NULL,
  `recipient_id` int(10) unsigned default NULL,
  `recipient_model_shortcut` varchar(255) default NULL,
  `ip` varchar(255) default NULL,
  `click_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `mail_component_id` (`mail_component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
