CREATE TABLE IF NOT EXISTS `kwc_basic_downloadtag` (
  `component_id` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `kwf_upload_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`component_id`),
  KEY `kwf_upload_id` (`kwf_upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
