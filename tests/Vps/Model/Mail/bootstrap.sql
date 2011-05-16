
CREATE TABLE IF NOT EXISTS `vps_enquiries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `save_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `is_spam` tinyint(1) NOT NULL default '0',
  `mail_sent` tinyint(1) NOT NULL default '0',
  `serialized_mail_vars` text,
  `serialized_mail_essentials` text,
  `mail_attachments` text NOT NULL,
  `sent_mail_content_text` text NOT NULL,
  `sent_mail_content_html` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 ;

INSERT INTO `vps_enquiries` (`id`, `save_date`, `is_spam`, `mail_sent`,
    `serialized_mail_vars`, `serialized_mail_essentials`,
    `mail_attachments`, `sent_mail_content_text`, `sent_mail_content_html`
) VALUES
(4, '2008-10-29 08:15:00', 0, 1, '""', '""', '""', 'mail text', 'mail html');
