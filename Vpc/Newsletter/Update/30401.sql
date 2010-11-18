CREATE TABLE IF NOT EXISTS `vpc_newsletter` (
    `id` smallint(6) NOT NULL auto_increment,
    `component_id` varchar(255) default NULL,
    `create_date` datetime NOT NULL,
    `status` enum('start','pause','stop','sending','finished') default NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `vpc_newsletter_log` (
    `id` int(11) NOT NULL auto_increment,
    `newsletter_id` smallint(6) NOT NULL,
    `start` datetime NOT NULL,
    `stop` datetime NOT NULL,
    `count` smallint(6) NOT NULL,
    `countErrors` smallint(6) NOT NULL,
    PRIMARY KEY  (`id`),
    KEY `newsletter_id` (`newsletter_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `vpc_newsletter_queue` (
    `id` int(11) NOT NULL auto_increment,
    `newsletter_id` smallint(6) NOT NULL,
    `recipient_model` varchar(255) NOT NULL,
    `recipient_id` varchar(255) NOT NULL,
    `searchtext` varchar(255) NOT NULL,
    `status` enum('queued','sending','userNotFound','sent','sendingError') NOT NULL default 'queued',
    `sent_date` timestamp NULL default NULL,
    PRIMARY KEY  (`id`),
    UNIQUE KEY `newsletter_id_2` (`newsletter_id`,`recipient_model`,`recipient_id`),
    KEY `newsletter_id` (`newsletter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

ALTER TABLE `vpc_newsletter_queue`
    ADD CONSTRAINT `vpc_newsletter_queue_ibfk_1` FOREIGN KEY (`newsletter_id`) REFERENCES `vpc_newsletter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `vpc_newsletter_subscribers` (
`id` int(10) unsigned NOT NULL auto_increment,
`gender` enum('','female','male') NOT NULL,
`title` varchar(255) NOT NULL,
`firstname` varchar(255) NOT NULL,
`lastname` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`format` enum('','html','text') NOT NULL,
`subscribe_date` datetime NOT NULL,
`unsubscribed` tinyint(1) NOT NULL,
`activated` tinyint( 1 ) NOT NULL DEFAULT '0',
PRIMARY KEY  (`id`)
) ENGINE=InnoDB ;



UPDATE `vpc_newsletter_queue` SET `recipient_model` = 'Vpc_Newsletter_Subscribe_Model'
	WHERE recipient_model='Vpc_Berlot_Newsletter_Subscribe_Model';
	