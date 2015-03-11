ALTER TABLE  `kwc_newsletter` ADD  `mails_per_minute` VARCHAR( 255 ) NOT NULL ,
ADD  `start_date` DATETIME NULL;
ALTER TABLE  `kwc_newsletter` CHANGE  `status`  `status` ENUM(  'start',  'startLater',  'pause',  'stop',  'sending',  'finished' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

CREATE TABLE  `kwc_newsletter_testmail_receiver` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`email` VARCHAR( 255 ) NOT NULL ,
`newsletter_component_id` VARCHAR( 255 ) NOT NULL ,
`last_sent_date` DATETIME NOT NULL
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
