#tags: kwc
ALTER TABLE  `cache_component` DROP PRIMARY KEY ,
ADD PRIMARY KEY (  `component_id` ,  `type` ,  `value` ,  `renderer` );
