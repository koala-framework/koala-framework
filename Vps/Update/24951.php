<?php
class Vps_Update_24951 extends Vps_Update
{
    public function update()
    {
        $sql = "DROP TABLE IF EXISTS `cache_component_meta`;";
        Zend_Registry::get('db')->query($sql);
        $sql = "CREATE TABLE IF NOT EXISTS `cache_component_meta` (
  `model` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `id` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `value` varchar(255) character set latin1 collate latin1_general_ci NOT NULL,
  `type` enum('cacheId','callback','componentClass') collate utf8_bin NOT NULL,
  PRIMARY KEY  (`model`,`id`,`value`,`type`),
  KEY `model` (`model`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
        Zend_Registry::get('db')->query($sql);
    }
}
