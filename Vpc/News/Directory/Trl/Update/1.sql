 CREATE TABLE `vpc_news_trl` (
`component_id` VARCHAR( 200 ) NOT NULL ,
`visible` TINYINT NOT NULL ,
`title` VARCHAR( 255 ) NOT NULL ,
`teaser` TEXT NOT NULL ,
PRIMARY KEY ( `component_id` )
) ENGINE = InnoDB ;
