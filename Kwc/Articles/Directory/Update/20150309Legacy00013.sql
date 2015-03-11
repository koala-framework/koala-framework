ALTER TABLE  `kwc_articles` ADD  `priority` TINYINT( 4 ) NOT NULL;

UPDATE `kwc_articles` SET `priority` = 1;
