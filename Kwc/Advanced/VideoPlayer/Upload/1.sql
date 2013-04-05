CREATE TABLE IF NOT EXISTS `kwc_advanced_video_player` (
  `component_id` varchar(255) NOT NULL,
  `mp4_kwf_upload_id` int(11) DEFAULT NULL,
  `ogg_kwf_upload_id` int(11) DEFAULT NULL,
  `webm_kwf_upload_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`component_id`),
  KEY `webm_kwf_upload_id` (`webm_kwf_upload_id`),
  KEY `ogg_kwf_upload_id` (`ogg_kwf_upload_id`),
  KEY `mp4_kwf_upload_id` (`mp4_kwf_upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8