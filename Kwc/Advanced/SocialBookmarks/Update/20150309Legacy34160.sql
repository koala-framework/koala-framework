 CREATE TABLE `kwc_socialbookmarks` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`component_id` VARCHAR( 200 ) NOT NULL ,
`network_id` VARCHAR( 100 ) NOT NULL ,
INDEX ( `component_id` )
) ENGINE = InnoDB ;
ALTER TABLE `kwc_socialbookmarks` ADD `pos` INT NOT NULL AFTER `component_id` ;
