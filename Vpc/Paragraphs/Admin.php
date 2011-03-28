<?php
class Vpc_Paragraphs_Admin extends Vpc_Admin
{
    public function gridColumns()
    {
        return array();
    }

    //gibts nimma
    protected final function _getComponents()
    {
    }

    public function setup()
    {
        $tablename = 'vpc_paragraphs';
        if (!$this->_tableExists($tablename)) {
            Vps_Registry::get('db')->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `component_id` varchar(255) NOT NULL,
                  `component` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }
}
