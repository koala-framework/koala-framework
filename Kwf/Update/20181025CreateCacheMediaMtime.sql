CREATE TABLE IF NOT EXISTS `cache_media_mtime` (
  `class` varchar(200) NOT NULL,
  `id` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `mtime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `cache_media_mtime`
 ADD PRIMARY KEY (`class`,`id`,`type`);
