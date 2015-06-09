CREATE TABLE IF NOT EXISTS `kwc_basic_image` (
  `component_id` varchar(255) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `enlarge` tinyint(3) DEFAULT '0',
  `kwf_upload_id` int(11) DEFAULT NULL,
  `dimension` varchar(200) DEFAULT NULL,
  `data` text NOT NULL,
  `scale` VARCHAR( 20 ) NOT NULL,
  PRIMARY KEY (`component_id`),
  KEY `kwf_upload_id` (`kwf_upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

