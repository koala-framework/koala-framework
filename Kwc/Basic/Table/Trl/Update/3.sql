ALTER TABLE `kwc_basic_table_data_trl` DROP PRIMARY KEY ;
ALTER TABLE `kwc_basic_table_data_trl` ADD PRIMARY KEY ( `id` );
ALTER TABLE `kwc_basic_table_data_trl` ADD `master_id` INT NOT NULL;