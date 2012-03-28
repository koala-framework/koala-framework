ALTER TABLE `cache_component` ADD `expanded_component_id` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL AFTER `page_db_id` ;
ALTER TABLE `cache_component` ADD INDEX ( `expanded_component_id` ) ;
