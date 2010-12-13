#tags: vpc
CREATE TABLE `cache_component_url_parents` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`page_id` VARCHAR( 200 ) NOT NULL ,
`parent_page_id` VARCHAR( 200 ) NOT NULL ,
INDEX ( `page_id` )
) ENGINE = MYISAM;
