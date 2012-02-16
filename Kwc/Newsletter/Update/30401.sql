CREATE TABLE IF NOT EXISTS `kwc_newsletter` (
    `id` smallint(6) NOT NULL auto_increment,
    `component_id` varchar(255) default NULL,
    `create_date` datetime NOT NULL,
    `status` enum('start','pause','stop','sending','finished') default NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `kwc_newsletter_log` (
    `id` int(11) NOT NULL auto_increment,
    `newsletter_id` smallint(6) NOT NULL,
    `start` datetime NOT NULL,
    `stop` datetime NOT NULL,
    `count` smallint(6) NOT NULL,
    `countErrors` smallint(6) NOT NULL,
    PRIMARY KEY  (`id`),
    KEY `newsletter_id` (`newsletter_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `kwc_newsletter_queue` (
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

ALTER TABLE `kwc_newsletter_queue`
    ADD CONSTRAINT `kwc_newsletter_queue_ibfk_1` FOREIGN KEY (`newsletter_id`) REFERENCES `kwc_newsletter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


UPDATE `kwc_newsletter_queue` SET `recipient_model` = 'Kwc_Newsletter_Subscribe_Model'
	WHERE recipient_model='Kwc_Berlot_Newsletter_Subscribe_Model';
	