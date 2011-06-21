CREATE TABLE IF NOT EXISTS `vpc_events_trl` (
  `component_id` varchar(200) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `teaser` text NOT NULL,
  `place` varchar(255) NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB ;
