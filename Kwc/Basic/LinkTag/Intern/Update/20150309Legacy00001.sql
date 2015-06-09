CREATE TABLE IF NOT EXISTS `kwc_basic_link_intern` (
  `component_id` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `rel` varchar(255) DEFAULT NULL,
  `param` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`component_id`),
  KEY `target` (`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
