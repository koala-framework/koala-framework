ALTER TABLE `cache_component` ADD `deleted` TINYINT( 1 ) NOT NULL ;
ALTER TABLE `cache_component` ADD INDEX ( `deleted` )  