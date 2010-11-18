CREATE TABLE IF NOT EXISTS vpc_composite_list_trl (
  `component_id` varchar(200) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY  (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
