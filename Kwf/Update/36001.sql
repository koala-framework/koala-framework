#tags: kwc
CREATE TABLE  `kwc_log_duplicate` (
`id` INT NOT NULL auto_increment,
`source_db_id` VARCHAR( 200 ) NOT NULL ,
`target_db_id` VARCHAR( 200 ) NOT NULL ,
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (  `id` ) ,
INDEX (  `source_db_id` )
) ENGINE = INNODB;
