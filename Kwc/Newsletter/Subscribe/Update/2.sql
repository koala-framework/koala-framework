#drop unique index
ALTER TABLE `kwc_newsletter_subscribers` DROP INDEX `email` ,
ADD INDEX `email` ( `email` ) ;
