CREATE TABLE `kwc_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `text` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB;

CREATE TABLE `kwc_components_to_tags`  (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `component_id` VARCHAR( 200 ) NOT NULL ,
  `tag_id` INT UNSIGNED NOT NULL ,
  INDEX ( `tag_id` )
) ENGINE = InnoDB ;

ALTER TABLE `kwc_components_to_tags` ADD FOREIGN KEY ( `tag_id` ) REFERENCES `kwc_tags` (
`id`
);

ALTER TABLE `kwc_components_to_tags` ADD INDEX ( `component_id` );

