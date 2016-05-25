CREATE TABLE IF NOT EXISTS `kwf_pools` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `pool` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `pos` smallint(5) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pool` (`pool`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `kwf_redirects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('path','domain','domainPath') NOT NULL,
  `source` varchar(255) NOT NULL,
  `target_type` enum('intern','extern','downloadTag') NOT NULL,
  `target` varchar(200) NOT NULL,
  `comment` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `source` (`source`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kwf_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `mime_type` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kwf_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` text NOT NULL,
  `language` varchar(5) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `password_salt` varchar(10) NOT NULL,
  `gender` enum('','female','male') NOT NULL,
  `title` varchar(100) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `deleted` tinyint NOT NULL,
  `logins` int NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lastname` (`lastname`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kwf_user_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `create_type` enum('auto','manual') NOT NULL,
  `message_type` varchar(255) NOT NULL,
  `message_date` datetime NOT NULL,
  `ip` varchar(255) NOT NULL,
  `by_user_id` int(10) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `kwf_welcome` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `kwf_upload_id` int(10) unsigned DEFAULT NULL,
  `login_kwf_upload_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vps_upload_id` (`kwf_upload_id`),
  KEY `login_vps_upload_id` (`login_kwf_upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;
