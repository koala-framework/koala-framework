CREATE TABLE IF NOT EXISTS `kwc_downloadstree_downloads` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `text` varchar(200) NOT NULL,
  `kwf_upload_id` int(10) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `kwf_upload_id` (`kwf_upload_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `kwc_downloadstree_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `component_id` varchar(200) NOT NULL,
  `pos` smallint(6) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `text` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `component_id` (`component_id`),
  KEY `visible` (`visible`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `kwc_downloadstree_projects_to_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

ALTER TABLE `kwc_downloadstree_downloads`
  ADD CONSTRAINT `kwc_downloadstree_downloads_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `kwc_downloadstree_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kwc_downloadstree_downloads_ibfk_4` FOREIGN KEY (`kwf_upload_id`) REFERENCES `kwf_uploads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `kwc_downloadstree_projects`
  ADD CONSTRAINT `kwc_downloadstree_projects_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `kwc_downloadstree_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `kwc_downloadstree_projects_to_users`
  ADD CONSTRAINT `kwc_downloadstree_projects_to_users_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `kwc_downloadstree_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
