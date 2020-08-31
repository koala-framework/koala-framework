CREATE TABLE IF NOT EXISTS `kwc_structured_data` (
  `component_id` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
