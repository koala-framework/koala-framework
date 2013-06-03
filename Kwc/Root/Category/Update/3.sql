ALTER TABLE  `kwf_pages` ADD  `parent_subroot_id` VARCHAR( 255 ) NOT NULL COMMENT 'cache' AFTER  `parent_id`;
ALTER TABLE  `kwf_pages` ADD INDEX (  `parent_subroot_id` );
ALTER TABLE  `kwf_pages` ADD INDEX (  `is_home` );
ALTER TABLE  `kwf_pages` ADD INDEX (  `filename` );
ALTER TABLE  `kwf_pages` ADD INDEX (  `component` );
