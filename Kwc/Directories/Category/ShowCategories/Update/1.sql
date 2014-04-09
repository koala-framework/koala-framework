CREATE TABLE `kwc_directories_category_showcategories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `component_id` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

ALTER TABLE `kwc_directories_category_showcategories`
  ADD CONSTRAINT `kwc_directories_category_showcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `kwc_directories_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

