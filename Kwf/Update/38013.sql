CREATE TABLE IF NOT EXISTS `kwf_logs` (
  `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `type` VARCHAR( 20 ) NOT NULL ,
  `exception` VARCHAR( 255 ) NOT NULL ,
  `thrown` VARCHAR( 255 ) NOT NULL ,
  `message` VARCHAR( 255 ) NOT NULL ,
  `exception_detail` TEXT NOT NULL ,
  `request_uri` VARCHAR( 255 ) NOT NULL ,
  `http_referer` VARCHAR( 255 ) NOT NULL ,
  `user` VARCHAR( 255 ) NOT NULL ,
  `useragent` VARCHAR( 255 ) NOT NULL ,
  `get` TEXT NOT NULL ,
  `post` TEXT NOT NULL ,
  `server` TEXT NOT NULL ,
  `files` TEXT NOT NULL ,
  `session` TEXT NOT NULL ,
  `date` DATETIME NOT NULL ,
  `filename` VARCHAR( 255 ) NOT NULL
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;