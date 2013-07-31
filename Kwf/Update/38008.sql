#tags: kwc
ALTER TABLE  `cache_component` CHANGE  `type`  `type` ENUM(  'page',  'component',  'master',  'partial',  'componentLink',  'fullPage' ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;

CREATE TABLE  `cache_component_includes` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `component_id` VARCHAR( 255 ) NOT NULL ,
    `target_id` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;
ALTER TABLE  `cache_component_includes` ADD  `type` VARCHAR( 50 ) NOT NULL;

ALTER TABLE `cache_component_includes` ADD INDEX  `source` (  `component_id` ,  `type` );
ALTER TABLE  `cache_component_includes` ADD INDEX (  `target_id` );
