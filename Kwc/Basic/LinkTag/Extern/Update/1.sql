CREATE TABLE IF NOT EXISTS `kwc_basic_link_extern` (
  `component_id` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `rel` varchar(255) DEFAULT NULL,
  `param` varchar(255) DEFAULT NULL,
  `open_type` enum('self','popup','blank') NOT NULL DEFAULT 'self',
  `width` mediumint(9) DEFAULT NULL,
  `height` mediumint(9) DEFAULT NULL,
  `menubar` tinyint(4) NOT NULL,
  `toolbar` tinyint(4) NOT NULL,
  `locationbar` tinyint(4) NOT NULL,
  `statusbar` tinyint(4) NOT NULL,
  `scrollbars` tinyint(4) NOT NULL,
  `resizable` tinyint(4) NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
