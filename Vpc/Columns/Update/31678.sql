CREATE TABLE `vpc_columns` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `component_id` VARCHAR( 200 ) NOT NULL ,
 `width` VARCHAR( 100 ) NOT NULL ,
 `pos` SMALLINT NOT NULL ,
 INDEX ( `component_id` ) 
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;