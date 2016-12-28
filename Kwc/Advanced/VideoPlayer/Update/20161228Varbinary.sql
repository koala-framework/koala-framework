ALTER TABLE `kwc_advanced_video_player` CHANGE `mp4_kwf_upload_id` `mp4_kwf_upload_id` VARBINARY(36) NULL DEFAULT NULL;
ALTER TABLE `kwc_advanced_video_player` CHANGE `ogg_kwf_upload_id` `ogg_kwf_upload_id` VARBINARY(36) NULL DEFAULT NULL;
ALTER TABLE `kwc_advanced_video_player` CHANGE `webm_kwf_upload_id` `webm_kwf_upload_id` VARBINARY(36) NULL DEFAULT NULL;
ALTER TABLE `kwc_advanced_video_player` ADD FOREIGN KEY (`mp4_kwf_upload_id`) REFERENCES `kwf_uploads`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `kwc_advanced_video_player` ADD FOREIGN KEY (`ogg_kwf_upload_id`) REFERENCES `kwf_uploads`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `kwc_advanced_video_player` ADD FOREIGN KEY (`webm_kwf_upload_id`) REFERENCES `kwf_uploads`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
