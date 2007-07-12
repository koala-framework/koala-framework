<?php
class Vpc_Formular_Password_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
        
    $fields['name'] = 'varchar(255) NOT NULL';
    $fields['length'] = 'smallint (6) NOT NULL';
    $fields['password'] = 'varchar (255) NOT NULL';
    $fields['size'] = 'smallint(6) NOT NULL'; 
    $this->createTable("component_formular_password", $fields);
        
    }
}