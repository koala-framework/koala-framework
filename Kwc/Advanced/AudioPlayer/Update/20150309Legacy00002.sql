ALTER TABLE `kwc_advanced_audio_player` ADD `auto_play` TINYINT NOT NULL ,
ADD `loop` TINYINT NOT NULL;

ALTER TABLE `kwc_advanced_audio_player` ADD `audio_width` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `mp3_kwf_upload_id` ,
ADD `audio_height` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `audio_width`;