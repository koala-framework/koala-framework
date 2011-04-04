#tags: vpc
ALTER TABLE `cache_component` CHANGE `type` `type` ENUM( 'page', 'component', 'master', 'partials', 'partial', 'mail', 'componentLink' ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
