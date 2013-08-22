ALTER TABLE  `kwc_newsletter_queue` ADD  `send_process_pid` INT NULL;
ALTER TABLE `kwc_newsletter_queue` DROP `status`;
