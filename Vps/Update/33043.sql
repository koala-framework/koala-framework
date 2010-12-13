#tags: vpc
CREATE TABLE IF NOT EXISTS `cache_component_processinput` (
  `page_id` varchar(200) NOT NULL,
  `process_component_ids` text NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
