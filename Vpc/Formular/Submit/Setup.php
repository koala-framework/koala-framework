<?php
class Vpc_Formular_Submit_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
       $fields['value'] = 'varchar(255) NOT NULL';
       
       $this->createTable('component_formular_submit', $fields);
    }
}