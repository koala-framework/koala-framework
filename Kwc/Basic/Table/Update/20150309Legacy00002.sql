ALTER TABLE `kwc_basic_table_data` ADD `visible` TINYINT NOT NULL;
UPDATE kwc_basic_table_data SET visible =1;