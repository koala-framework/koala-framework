#tags: kwc
ALTER TABLE  `cache_component` CHANGE  `type`  `type` ENUM(  'page',  'component',  'master',  'partial',  'componentLink',  'fullPage', 'partials' ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;
