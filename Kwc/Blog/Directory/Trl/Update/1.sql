CREATE TABLE `kwc_blog_trl` (
`component_id` VARCHAR(200) NOT NULL,
`visible` TINYINT(4) NOT NULL,
`title` VARCHAR(255) NOT NULL,
PRIMARY KEY (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
