<?php
class Vps_Update_25283 extends Vps_Update
{
    public function update()
    {
        $sql = "DROP TABLE IF EXISTS `cache_component`;";
        Zend_Registry::get('db')->query($sql);
        $sql = "CREATE TABLE IF NOT EXISTS `cache_component` (
  `id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `page_id` varchar(200) character set latin1 collate latin1_general_ci NOT NULL,
  `component_class` varchar(100) character set latin1 collate latin1_general_ci NOT NULL,
  `content` text character set utf8 NOT NULL,
  `last_modified` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `component_class` (`component_class`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
        Zend_Registry::get('db')->query($sql);
    }
}
