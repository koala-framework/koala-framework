<?php
class Vpc_Form_Dynamic_Admin extends Vpc_Paragraphs_Admin
{
    public function setup()
    {
        $tablename = 'vpc_formular';
        if (!$this->_tableExists($tablename)) {
            Vps_Registry::get('db')->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `component_id` varchar(255) NOT NULL,
                  `parent_id` int(10) unsigned NULL,
                  `class` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL,
                  `settings` text NOT NULL,
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }
}
