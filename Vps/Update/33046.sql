#tags: vpc
CREATE TABLE `cache_component_url` (
`url` VARCHAR( 255 ) NOT NULL ,
`page_id` VARCHAR( 200 ) NOT NULL ,
PRIMARY KEY ( `url` )
) ENGINE = MYISAM;
