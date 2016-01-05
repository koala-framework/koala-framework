ALTER TABLE  `kwf_uploads` CHANGE  `is_image`  `is_image` TINYINT( 4 ) NULL;
UPDATE  `kwf_uploads` SET is_image = NULL WHERE is_image = -1;