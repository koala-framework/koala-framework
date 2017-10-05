ALTER TABLE `kwc_newsletter_subscriber_logs` ADD `state` ENUM('subscribed', 'activated', 'unsubscribed') NULL AFTER `ip`;
