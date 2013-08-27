ALTER TABLE  `kwc_newsletter_queue` ADD  `send_process_pid` INT NULL;
DELETE FROM`kwc_newsletter_queue` WHERE `status` = 'sending';
ALTER TABLE `kwc_newsletter_queue` DROP `status`;
ALTER TABLE  `kwc_newsletter` ADD  `resume_date` DATETIME NULL;
ALTER TABLE  `kwc_newsletter_queue_log` ADD INDEX  `count` (  `newsletter_id` ,  `send_date` );
ALTER TABLE  `kwc_newsletter` ADD INDEX (  `status` );
ALTER TABLE `kwc_newsletter_queue_log` DROP `searchtext`;
