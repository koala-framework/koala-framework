ALTER TABLE `kwc_news_to_categories` DROP FOREIGN KEY `kwc_news_to_categories_ibfk_1` ,
ADD FOREIGN KEY ( `news_id` ) REFERENCES `kwc_news` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `kwc_news_to_categories` DROP FOREIGN KEY `kwc_news_to_categories_ibfk_2` ,
ADD FOREIGN KEY ( `category_id` ) REFERENCES `kwc_directories_categories` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;
