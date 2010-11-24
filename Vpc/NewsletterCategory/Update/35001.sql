RENAME TABLE vpc_newsletter_categories TO vpc_newsletter_subscribecategories;
ALTER TABLE `vpc_newsletter_subscribecategories` CHANGE `category` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `vpc_newsletter_subscribecategories` CHANGE `vps_pool_id` `category_id` SMALLINT( 5 ) UNSIGNED NOT NULL;
  
CREATE TABLE IF NOT EXISTS `vpc_newsletter_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pos` tinyint(4) NOT NULL,
  `category` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

RENAME TABLE `vpc_newsletter_subscribers_to_pool`  TO `vpc_newsletter_subscribers_to_category`;
ALTER TABLE `vpc_newsletter_subscribers_to_category` CHANGE `pool_id` `category_id` SMALLINT UNSIGNED NOT NULL;
