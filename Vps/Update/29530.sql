
ALTER TABLE `cache_users` ADD INDEX ( `deleted` );
ALTER TABLE `cache_users` ADD INDEX ( `locked` );
ALTER TABLE `cache_users` DROP INDEX `email_2`;
