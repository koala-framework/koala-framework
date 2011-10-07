CREATE TABLE IF NOT EXISTS `vpc_basic_table` (
  `component_id` varchar(255) NOT NULL,
  `columns` smallint(6) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `vpc_basic_table_data` (
  `id` int(11) NOT NULL auto_increment,
  `component_id` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `pos` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
