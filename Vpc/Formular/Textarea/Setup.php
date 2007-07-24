<?php
class Vpc_Formular_Textarea_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
       $fields['value'] = 'varchar(255) NOT NULL'; 
       $fields['cols'] = 'smallint(6) NOT NULL';
       $fields['rows'] = 'smallint(6) NOT NULL';       
       
       $this->createTable('component_formular_textarea', $fields);
    }
}