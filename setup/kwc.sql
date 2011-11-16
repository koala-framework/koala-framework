CREATE TABLE IF NOT EXISTS `cache_component` (
  `component_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `db_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `page_db_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `component_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type` enum('page','component','master','partials','partial','mail','componentLink') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Bei Partial partialId oder bei master component_id zu der das master gehört',
  `expire` int(11) DEFAULT NULL,
  `deleted` smallint(1) NOT NULL DEFAULT '0',
  `content` longtext NOT NULL,
  PRIMARY KEY (`component_id`,`type`,`value`),
  KEY `component_class` (`component_class`),
  KEY `db_id` (`db_id`),
  KEY `page_db_id` (`page_db_id`),
  KEY `value` (`value`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache_component_url` (
  `url` varchar(255) NOT NULL,
  `page_id` varchar(200) NOT NULL,
  PRIMARY KEY (`url`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `kwc_data` (
  `component_id` varchar(255) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kwf_enquiries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `save_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_spam` tinyint(1) NOT NULL DEFAULT '0',
  `mail_sent` tinyint(1) NOT NULL DEFAULT '0',
  `serialized_mail_vars` text,
  `serialized_mail_essentials` text,
  `mail_attachments` text NOT NULL,
  `sent_mail_content_text` text NOT NULL,
  `sent_mail_content_html` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
