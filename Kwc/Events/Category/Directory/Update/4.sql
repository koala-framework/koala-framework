ALTER TABLE `kwc_events`  ENGINE =  InnoDB;  
ALTER TABLE `kwc_events_to_categories` CHANGE `event_id` `event_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `kwc_events_to_categories` ADD FOREIGN KEY ( `event_id` ) REFERENCES `kwc_events` (`id`);
ALTER TABLE `kwc_events_to_categories` ADD FOREIGN KEY ( `category_id` ) REFERENCES `kwc_directories_categories` (`id`);
