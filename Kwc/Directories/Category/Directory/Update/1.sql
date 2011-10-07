CREATE TABLE `vpc_directories_categories` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `pos` SMALLINT NOT NULL ,
 `name` VARCHAR( 200 ) NOT NULL ,
 `visible` TINYINT NOT NULL ,
 INDEX ( `visible` )
) ENGINE = INNODB;
ALTER TABLE `vpc_directories_categories` ADD `component_id` VARCHAR( 200 ) NOT NULL AFTER `id` ;
ALTER TABLE `vpc_directories_categories` ADD INDEX ( `component_id` ) ;
