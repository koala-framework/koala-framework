CREATE TABLE `kwc_mail_views` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`mail_component_id` VARCHAR( 255 ) NOT NULL ,
`recipient_id` INT( 11 ) NOT NULL ,
`recipient_model_shortcut` VARCHAR( 255 ) NOT NULL ,
`ip` VARCHAR( 255 ) NOT NULL ,
`date` DATETIME NOT NULL ,
INDEX (  `mail_component_id` )
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
