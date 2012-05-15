#tags: kwc
ALTER TABLE `cache_component` ADD `expanded_component_id` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL AFTER `page_db_id` ;
ALTER TABLE `cache_component` ADD INDEX ( `expanded_component_id` ) ;
ALTER TABLE `cache_component` CHANGE `page_db_id` `page_db_id` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;
ALTER TABLE `cache_component` CHANGE `component_id` `component_id` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;
ALTER TABLE `cache_component` CHANGE `db_id` `db_id` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;
ALTER TABLE `cache_component` CHANGE `component_class` `component_class` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;
ALTER TABLE `cache_component` CHANGE `value` `value` VARCHAR( 20 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;
ALTER TABLE `cache_component_url` ADD `expanded_page_id` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;
ALTER TABLE `cache_component_url` ADD INDEX ( `expanded_page_id` ) ;
ALTER TABLE `cache_component_url` CHANGE `page_id` `page_id` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;
ALTER TABLE `cache_component_url` CHANGE `url` `url` VARCHAR( 255 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ;


