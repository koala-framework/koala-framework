ALTER TABLE `kwc_advanced_video_player` ADD `auto_play` TINYINT NOT NULL ,
ADD `loop` TINYINT NOT NULL;

ALTER TABLE `kwc_advanced_video_player` ADD `video_width` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `webm_kwf_upload_id` ,
ADD `video_height` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `video_width`;