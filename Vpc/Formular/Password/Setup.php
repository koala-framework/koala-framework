<?php
class Vpc_Formular_Password_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
	    $fields['maxlength'] = 'smallint (6) NOT NULL';
	    $fields['width'] = 'smallint(6) NOT NULL'; 
	    $this->createTable("component_formular_password", $fields);
        
    }
}