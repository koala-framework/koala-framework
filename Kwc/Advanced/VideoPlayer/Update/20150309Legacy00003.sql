ALTER TABLE `kwc_advanced_video_player` ADD `source_type` VARCHAR( 255 ) NOT NULL AFTER `component_id`;
UPDATE kwc_advanced_video_player SET source_type = 'files';
ALTER TABLE `kwc_advanced_video_player` ADD `mp4_url` TEXT NOT NULL AFTER `webm_kwf_upload_id` ,
ADD `ogg_url` TEXT NOT NULL AFTER `mp4_url` ,
ADD `webm_url` TEXT NOT NULL AFTER `ogg_url`;
