ALTER TABLE `vpc_news_to_categories` CHANGE `news_id` `news_id` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `vpc_news_to_categories` ADD FOREIGN KEY ( `news_id` ) REFERENCES `vpc_news` (`id`);
ALTER TABLE `vpc_news_to_categories` ADD FOREIGN KEY ( `category_id` ) REFERENCES `vpc_directories_categories` (`id`);
