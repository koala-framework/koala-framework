ALTER TABLE `kwc_news_to_categories` CHANGE `news_id` `news_id` SMALLINT NOT NULL DEFAULT '0';
DELETE FROM `kwc_news_to_categories` WHERE news_id NOT IN (SELECT id FROM kwc_news);
ALTER TABLE `kwc_news_to_categories` ADD FOREIGN KEY ( `news_id` ) REFERENCES `kwc_news` (`id`);
ALTER TABLE `kwc_news_to_categories` ADD FOREIGN KEY ( `category_id` ) REFERENCES `kwc_directories_categories` (`id`);
