ALTER TABLE `kwc_newsletter` ADD `count_sent` INT NULL ;
ALTER TABLE `kwc_newsletter` ADD `last_sent_date` DATETIME NULL ;
UPDATE `kwc_newsletter` n SET count_sent = ( SELECT count( * )
    FROM kwc_newsletter_queue q
    WHERE newsletter_id = n.id
    AND STATUS = 'sent' ) ;
UPDATE `kwc_newsletter` n SET last_sent_date = ( SELECT max(sent_date)
    FROM kwc_newsletter_queue q
    WHERE newsletter_id = n.id
    AND STATUS = 'sent' ) ;
DELETE FROM `kwc_newsletter_queue` WHERE STATUS = 'sent';
DELETE FROM `kwc_newsletter_queue` WHERE STATUS = 'sending';
DELETE FROM `kwc_newsletter_queue` WHERE STATUS = 'userNotFound';
ALTER TABLE `kwc_newsletter_queue` CHANGE `status` `status` ENUM( 'queued', 'sending' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'queued';
ALTER TABLE `kwc_newsletter_queue` DROP `sent_date`;
