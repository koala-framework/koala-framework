#tags: kwf
ALTER TABLE `kwf_redirects` ADD `domain_component_id` VARCHAR( 200 ) NULL AFTER `id` ;
ALTER TABLE `kwf_redirects` ADD INDEX ( `domain_component_id` ) ;
