ALTER TABLE `kwc_basic_image` CHANGE `crop_x` `crop_x` INT( 11 ) NULL;
ALTER TABLE `kwc_basic_image` CHANGE `crop_y` `crop_y` INT( 11 ) NULL;
ALTER TABLE `kwc_basic_image` CHANGE `crop_width` `crop_width` INT( 11 ) NULL;
ALTER TABLE `kwc_basic_image` CHANGE `crop_height` `crop_height` INT( 11 ) NULL;

update kwc_basic_image set crop_x=null, crop_y=null, crop_width=null, crop_height=null;
