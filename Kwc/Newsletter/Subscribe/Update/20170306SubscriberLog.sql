CREATE TABLE IF NOT EXISTS `kwc_newsletter_subscriber_logs` (
  `id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) NULL,
  `message` text NOT NULL,
  `source` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `kwc_newsletter_subscriber_logs`
 ADD PRIMARY KEY (`id`), ADD KEY `subscriber_id` (`subscriber_id`);

ALTER TABLE `kwc_newsletter_subscriber_logs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
