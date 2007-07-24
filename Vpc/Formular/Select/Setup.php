<?php
class Vpc_Formular_Multicheckbox_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
        $fields['horizontal'] = 'tinyint(4) NOT NULL';
        $this->createTable('component_formular_multicheckbox', $fields);
        
        $tablename = 'component_formular_multicheckbox_checkboxes';
        if (!$this->_tableExits($tablename)) {
	        $this->_db->query("CREATE TABLE `$tablename` (
	  						   `id` int(10) unsigned NOT NULL auto_increment,
	                           `component_key` varchar(100) NOT NULL,
	                           `page_key` varchar(100) NOT NULL,
	                           `component_id` int(10) unsigned NOT NULL,
	                           PRIMARY KEY  (`id`),
	                           KEY `component_key` (`component_key`,`page_key`,`component_id`))
							   ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=8;");
	        }
    }
}