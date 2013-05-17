CREATE TABLE IF NOT EXISTS `kwc_directory_categories_to_components` (
  `component_id` varchar(200) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`component_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `kwc_directory_categories_to_components`
  ADD CONSTRAINT `kwc_directory_categories_to_components_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `kwc_directory_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
