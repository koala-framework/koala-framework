ALTER TABLE `vpc_basic_table_data` ADD `visible` TINYINT NOT NULL;
UPDATE vpc_basic_table_data SET visible =1;