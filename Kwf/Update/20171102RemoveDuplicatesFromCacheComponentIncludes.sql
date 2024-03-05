CREATE TABLE `cache_component_includes_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` varchar(255) NOT NULL,
  `target_id` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),   KEY `source` (`component_id`,`type`),
   KEY `target_id` (`target_id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO cache_component_includes_temp(component_id, target_id, type)
    SELECT DISTINCT component_id, target_id, type
    FROM cache_component_includes;

DROP TABLE `cache_component_includes`;
RENAME TABLE `cache_component_includes_temp` TO `cache_component_includes`;
