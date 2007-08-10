<?php
class Vpc_Formular_SelectMultiple_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['rows'] = "smallint(6) NOT NULL";
        $this->createTable('component_formular_selectmultiple', $fields);

        $tablename = 'component_formular_selectmultiple_options';
        if (!$this->_tableExits($tablename)) {
	        $this->_db->query("CREATE TABLE `$tablename` (
                              `id` int(11) NOT NULL auto_increment,
							  `page_id` int(11) NOT NULL,
							  `component_key` varchar(255) NOT NULL,
							  `value` varchar(255) NOT NULL,
							  `selected` tinyint(4) NOT NULL,
							  PRIMARY KEY  (`id`),
							  KEY `KEY` (`page_id`,`component_key`)
							) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
        }
    }
}