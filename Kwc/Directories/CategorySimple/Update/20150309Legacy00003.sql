CREATE TABLE IF NOT EXISTS `kwc_directory_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `pos` smallint(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `kwc_directory_categories_to_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

ALTER TABLE `kwc_directory_categories` ADD FOREIGN KEY ( `parent_id` ) 
    REFERENCES `kwc_directory_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `kwc_directory_categories_to_items` ADD FOREIGN KEY ( `category_id` ) 
    REFERENCES `kwc_directory_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
