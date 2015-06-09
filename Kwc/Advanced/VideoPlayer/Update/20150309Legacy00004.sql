ALTER TABLE  `kwc_advanced_video_player` ADD  `format` ENUM(  '',  '16x9',  '4x3' ) NOT NULL;
ALTER TABLE  `kwc_advanced_video_player` ADD  `size` ENUM(  'contentWidth',  'userDefined' ) NOT NULL AFTER  `webm_kwf_upload_id`;

UPDATE  `kwc_advanced_video_player` SET  `size` =  IF (`video_width` =  '100%' OR `video_height` = '100%', 'contentWidth', 'userDefined'),
    `video_width` =  IF (`video_width` =  '100%' OR `video_height` = '100%', '', video_width),
    `video_height` =  IF (`video_width` =  '100%' OR `video_height` = '100%', '', video_height)
;

ALTER TABLE  `kwc_advanced_video_player` CHANGE  `video_width`  `video_width` INT( 11 ) NOT NULL;
ALTER TABLE  `kwc_advanced_video_player` CHANGE  `video_height`  `video_height` INT( 11 ) NOT NULL;
