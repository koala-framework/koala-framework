CREATE TABLE `kwf_sessions` (
  `sessionId` varchar(32) NOT NULL,
  `expiration` int(10) unsigned NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY (`sessionId`),
  KEY `expiration` (`expiration`)
) ENGINE=InnoDB;
