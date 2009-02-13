<?php
class Vps_Update_24725 extends Vps_Update
{
    public function update()
    {
        parent::update();
        $db = Zend_Registry::get('db');
        $db->query("CREATE TABLE IF NOT EXISTS `cache_component_meta` (
  `model` varchar(200) collate utf8_bin NOT NULL,
  `id` varchar(255) collate utf8_bin default NULL,
  `cache_id` varchar(255) collate utf8_bin default NULL,
  `component_class` varchar(200) collate utf8_bin default NULL,
  KEY `model` (`model`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
    }
}
