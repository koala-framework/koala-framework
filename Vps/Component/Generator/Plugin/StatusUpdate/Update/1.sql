CREATE TABLE `vpc_statusupdate_auth` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `type` VARCHAR( 50 ) NOT NULL ,
 `auth_token` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
 INDEX ( `type` )
) ENGINE = INNODB;

 CREATE TABLE `vpc_statusupdate_log` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`type` VARCHAR( 50 ) NOT NULL ,
`message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`date` DATETIME NOT NULL ,
`data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB ;

ALTER TABLE `vpc_statusupdate_log` ADD `component_id` VARCHAR( 200 ) NOT NULL AFTER `id` ;

ALTER TABLE `vpc_statusupdate_log` ADD INDEX ( `component_id` ) ;

