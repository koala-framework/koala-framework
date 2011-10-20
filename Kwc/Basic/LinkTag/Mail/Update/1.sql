CREATE TABLE IF NOT EXISTS `kwc_basic_link_mail` (
  `component_id` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
