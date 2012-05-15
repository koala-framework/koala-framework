#tags: fulltext
CREATE TABLE IF NOT EXISTS `kwc_fulltext_meta` (
`page_id` VARCHAR( 200 ) NOT NULL ,
 `indexed_date` DATETIME NULL ,
 `changed_date` DATETIME NULL ,
 `changed_recursive` TINYINT NOT NULL ,
 PRIMARY KEY ( `page_id` )
) ENGINE = INNODB;
