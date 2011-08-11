CREATE TABLE `vpc_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `text` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB;

CREATE TABLE `vpc_components_to_tags`  (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `component_id` VARCHAR( 200 ) NOT NULL ,
  `tag_id` INT UNSIGNED NOT NULL ,
  INDEX ( `tag_id` )
) ENGINE = InnoDB ;

ALTER TABLE `vpc_components_to_tags` ADD FOREIGN KEY ( `tag_id` ) REFERENCES `vpc_tags` (
`id`
);

ALTER TABLE `vpc_components_to_tags` ADD INDEX ( `component_id` );

