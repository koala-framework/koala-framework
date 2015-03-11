CREATE TABLE IF NOT EXISTS `kwc_childpages_teaser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `child_id` varchar(255) NOT NULL,
  `component_id` varchar(255) NOT NULL,
  `target_page_id` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
ALTER TABLE  `kwc_childpages_teaser` ADD  `pos` INT NOT NULL AFTER  `component_id`;
