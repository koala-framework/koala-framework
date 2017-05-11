CREATE TABLE `kwf_maintenance_job_runs` (
  `id` int(11) NOT NULL,
  `job` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `start` datetime NOT NULL,
  `runtime` int(11) NOT NULL,
  `log` text NOT NULL,
  `last_process_seen` datetime NOT NULL,
  `pid` int(11) NOT NULL,
  `progress` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `kwf_maintenance_job_runs` ADD PRIMARY KEY (`id`);

ALTER TABLE `kwf_maintenance_job_runs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
