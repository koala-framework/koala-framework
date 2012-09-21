#tags: kwc
ALTER TABLE `cache_component` ADD `renderer` ENUM( 'component', 'mail_html', 'mail_txt' ) NOT NULL AFTER `component_class` ;
ALTER TABLE `cache_component` CHANGE `type` `type` ENUM( 'page', 'component', 'master', 'partial', 'componentLink' ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
