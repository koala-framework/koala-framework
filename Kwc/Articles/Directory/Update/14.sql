ALTER TABLE `kwc_articles` ADD `is_top_expire` DATE NULL AFTER `is_top` ;
ALTER TABLE `kwc_articles` CHANGE `is_top` `is_top_checked` TINYINT( 4 ) NOT NULL; 