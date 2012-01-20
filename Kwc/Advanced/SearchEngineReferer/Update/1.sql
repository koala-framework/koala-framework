CREATE TABLE IF NOT EXISTS `kwc_advanced_se_referer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `referer_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDb  DEFAULT CHARSET=utf8 ;
 