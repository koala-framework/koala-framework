<?php
class Vpc_Formular_HiddenField_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
        $fields['name'] = 'varchar(255) NOT NULL';
        $this->createTable('component_formular_hiddenfield', $fields);
    }
}