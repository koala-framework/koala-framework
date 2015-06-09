ALTER TABLE `kwc_basic_table_data_trl` DROP PRIMARY KEY ;
ALTER TABLE `kwc_basic_table_data_trl` ADD `master_id` INT NOT NULL;
UPDATE `kwc_basic_table_data_trl` SET master_id=id;
ALTER TABLE  `kwc_basic_table_data_trl` ADD INDEX (  `id` );
ALTER TABLE  `kwc_basic_table_data_trl` CHANGE  `id`  `id` INT( 11 ) NULL;
UPDATE `kwc_basic_table_data_trl` SET id=NULL;
ALTER TABLE  `kwc_basic_table_data_trl` CHANGE  `id`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `kwc_basic_table_data_trl` ADD PRIMARY KEY ( `id` );
ALTER TABLE  `kwc_basic_table_data_trl` DROP INDEX  `id`;
