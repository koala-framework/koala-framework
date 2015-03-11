CREATE TABLE IF NOT EXISTS `kwc_composite_text_image_link` (
  `component_id` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `teaser` text NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
