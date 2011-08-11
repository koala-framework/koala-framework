#tags: vpc
TRUNCATE TABLE `cache_component`;
TRUNCATE TABLE `cache_componentpreload`;
ALTER TABLE `cache_component` ADD `nocache` TINYINT NOT NULL AFTER `value` ;
