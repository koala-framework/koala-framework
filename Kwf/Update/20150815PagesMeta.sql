#tags: kwc
CREATE TABLE IF NOT EXISTS `kwf_pages_meta` (
  `page_id` varchar(255) NOT NULL,
  `domain_component_id` varchar(255) NOT NULL,
  `subroot_component_id` varchar(255) NOT NULL,
  `expanded_component_id` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `changed_date` datetime DEFAULT NULL,
  `changed_recursive` tinyint(4) NOT NULL,
  `fulltext_indexed_date` datetime DEFAULT NULL,
  `meta_noindex` tinyint(4) NOT NULL,
  `fulltext_skip` tinyint(4) NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `domain_component_id` (`domain_component_id`),
  KEY `deleted` (`deleted`),
  KEY `expanded_component_id` (`expanded_component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS kwc_fulltext_meta;
