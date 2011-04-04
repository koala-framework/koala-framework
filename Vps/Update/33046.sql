#tags: vpc
DROP TABLE IF EXISTS `cache_component_url`;
CREATE TABLE IF NOT EXISTS `cache_component_url` (
  `url` varchar(255) NOT NULL,
  `page_id` varchar(200) NOT NULL,
  `page` text NOT NULL,
  PRIMARY KEY (`url`)
) ENGINE = MYISAM;
