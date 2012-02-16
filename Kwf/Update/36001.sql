#tags: kwc
CREATE TABLE  `kwc_log_duplicate` (
`id` INT NOT NULL auto_increment,
`source_component_id` VARCHAR( 200 ) NOT NULL ,
`target_component_id` VARCHAR( 200 ) NOT NULL ,
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (  `id` ) ,
INDEX (  `source_component_id` )
) ENGINE = INNODB;
ALTER TABLE  `kwc_log_duplicate` ADD INDEX (  `target_component_id` );
