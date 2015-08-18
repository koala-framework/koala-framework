CREATE TABLE IF NOT EXISTS `kwf_pages_trl_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

ALTER TABLE `kwf_pages_trl_history`
  ADD CONSTRAINT `kwf_pages_trl_history_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `kwf_pages_trl` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE;
