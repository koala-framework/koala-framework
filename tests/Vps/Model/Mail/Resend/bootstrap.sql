
CREATE TABLE IF NOT EXISTS `vps_enquiries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `save_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `is_spam` tinyint(1) NOT NULL default '0',
  `mail_sent` tinyint(1) NOT NULL default '0',
  `serialized_mail_vars` text,
  `serialized_mail_essentials` text,
  `mail_attachments` text,
  `sent_mail_content_text` text NOT NULL,
  `sent_mail_content_html` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
