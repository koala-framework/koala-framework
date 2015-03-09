CREATE TABLE IF NOT EXISTS `kwc_advanced_audio_player` (
  `component_id` varchar(255) NOT NULL,
  `mp3_kwf_upload_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`component_id`),
  KEY `mp3_kwf_upload_id` (`mp3_kwf_upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;