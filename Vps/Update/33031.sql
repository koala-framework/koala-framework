TRUNCATE TABLE `cache_component`;
ALTER TABLE `cache_component` DROP INDEX `deleted`;
ALTER TABLE `cache_component` ADD INDEX ( `page_id` );