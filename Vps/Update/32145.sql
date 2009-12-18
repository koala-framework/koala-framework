#tags: vps 
CREATE TABLE IF NOT EXISTS `vps_user_messages` (
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
) ENGINE=InnoDB ;
