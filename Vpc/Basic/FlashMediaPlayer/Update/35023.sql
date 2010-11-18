CREATE TABLE IF NOT EXISTS `vpc_basic_flashmediaplayer` (
  `component_id` varchar(255) NOT NULL,
  `vps_upload_id_media` int(11) default NULL,
  `width` smallint(6) NOT NULL,
  `height` smallint(6) NOT NULL,
  `autostart` tinyint(1) NOT NULL default '0',
  `loop` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;