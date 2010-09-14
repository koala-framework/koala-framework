ALTER TABLE `vpc_events`  ENGINE =  InnoDB;  
ALTER TABLE `vpc_events_to_categories` CHANGE `event_id` `event_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `vpc_events_to_categories` ADD FOREIGN KEY ( `event_id` ) REFERENCES `vpc_events` (`id`);
ALTER TABLE `vpc_events_to_categories` ADD FOREIGN KEY ( `category_id` ) REFERENCES `vpc_directories_categories` (`id`);
