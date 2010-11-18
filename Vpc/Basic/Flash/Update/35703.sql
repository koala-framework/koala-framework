
ALTER TABLE `vpc_basic_flash` ADD `external_flash_url` VARCHAR( 255 ) NULL AFTER `vps_upload_id_media` ;
ALTER TABLE `vpc_basic_flash` ADD `flash_source_type` VARCHAR( 255 ) NOT NULL AFTER `component_id` ;
