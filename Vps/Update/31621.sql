#tags: newsletter
ALTER TABLE `vpc_newsletter_queue` CHANGE `status` `status` ENUM( 'queued', 'sending', 'userNotFound', 'noAddress', 'sent', 'sendingError' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'queued';
