CREATE TABLE IF NOT EXISTS `kwc_newsletter_queue_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_id` smallint(6) NOT NULL,
  `recipient_model` varchar(255) NOT NULL,
  `recipient_id` varchar(255) NOT NULL,
  `searchtext` varchar(255) NOT NULL,
  `status` enum('sent','failed','usernotfound') NOT NULL DEFAULT 'sent',
  `send_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
