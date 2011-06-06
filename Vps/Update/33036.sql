#tags: vps
DROP TABLE IF EXISTS `vps_redirects`;
CREATE TABLE  `vps_redirects` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`type` ENUM(  'path','domain', 'domainPath' ) NOT NULL ,
`source` TEXT NOT NULL ,
`target` VARCHAR( 200 ) NOT NULL ,
`comment` TEXT NOT NULL ,
`active` TINYINT NOT NULL ,
INDEX (  `type` )
) ENGINE = INNODB;
ALTER TABLE  `vps_redirects` CHANGE  `source`  `source` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE  `vps_redirects` ADD INDEX (  `source` );
ALTER TABLE  `vps_redirects` ADD INDEX (  `active` );
