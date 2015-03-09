CREATE TABLE IF NOT EXISTS `kwc_composite_textimages` (
  `component_id` varchar(255) NOT NULL,
  `image_position` enum('left','right','alternate') DEFAULT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
