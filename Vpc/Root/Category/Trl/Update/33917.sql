CREATE TABLE IF NOT EXISTS `vps_pages_trl` (
  `component_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `custom_filename` tinyint(4) NOT NULL,
  PRIMARY KEY  (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
