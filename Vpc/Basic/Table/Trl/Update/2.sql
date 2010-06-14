TRUNCATE TABLE `vpc_basic_table_data_trl` ;
ALTER TABLE `vpc_basic_table_data_trl` ADD `component_id` VARCHAR( 255 ) NOT NULL AFTER `id` ;
ALTER TABLE `vpc_basic_table_data_trl` DROP PRIMARY KEY  ;
 ALTER TABLE `vpc_basic_table_data_trl` ADD PRIMARY KEY ( `id` , `component_id` );