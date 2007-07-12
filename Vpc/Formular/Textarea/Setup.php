<?php
class Vpc_Formular_Textarea_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
        
       $fields['name'] = 'varchar(255) NOT NULL'; 
       $fields['cols'] = 'smallint(6) NOT NULL';
       $fields['rows'] = 'smallint(6) NOT NULL';
       $fields['text'] = 'varchar(255) NOT NULL';
       
       $this->createTable('component_formular_textarea', $fields);
        
	   /*$sql ="CREATE TABLE `component_formular_textarea` (
	  		`id` int(10) unsigned NOT NULL,
	  		`page_key` varchar(255) NOT NULL,
	  		`component_key` varchar(255) NOT NULL,
			`name` varchar(255) NOT NULL,	
	  		`cols` smallint(6) NOT NULL,
			`rows` smallint(6) NOT NULL,
	  		`text` varchar(255) default NULL,	  		
	 		PRIMARY KEY  (`id`,`page_key`,`component_key`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    
        $db->query($sql);*/
    }
}