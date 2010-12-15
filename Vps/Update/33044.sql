#tags: vpc
DROP TABLE IF EXISTS `cache_component_url_parents`;
CREATE TABLE `cache_component_url_parents` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`page_id` VARCHAR( 200 ) NOT NULL ,
`parent_page_id` VARCHAR( 200 ) NOT NULL ,
INDEX ( `page_id` )
) ENGINE = MYISAM;
