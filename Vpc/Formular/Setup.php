<?php
class Vpc_Formular_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $tablename = 'vpc_formular';
        if (!$this->_tableExits($tablename)) {
          $this->_db->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `page_id` int(10) unsigned NOT NULL,
                  `component_key` varchar(255) NOT NULL,
                  `component_class` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `mandatory` tinyint(4) NOT NULL,
                  `no_cols` tinyint(4) NOT NULL,
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;");
        }

        $this->copyTemplate('Formular.html');
    }
}