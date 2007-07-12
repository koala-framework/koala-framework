<?php
class Vpc_Formular_Option_Setup extends Vpc_Setup_Abstract 
{
        
    public function setup()
    {   
	    $fields['name'] = 'varchar(255) NOT NULL';
	    $fields['value'] = 'varchar(255) NOT NULL';
	    $fields['text'] = 'varchar (255) NOT NULL';
	 
	    $this->createTable('component_formular_option', $fields);

	 
    }
}