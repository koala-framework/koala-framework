CREATE TABLE IF NOT EXISTS `vpc_directories_top` (
  `component_id` varchar(255) NOT NULL,
  `directory_component_id` varchar(255) default NULL,
  PRIMARY KEY  (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;