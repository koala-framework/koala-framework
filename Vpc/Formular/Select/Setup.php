<?php
class Vpc_Formular_Select_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
        $fields['name'] = 'varchar(255) NOT NULL';
        $fields['multiple'] = 'tinyint(1) NOT NULL';
        $fields['size'] = "smallint(6) NOT NULL";
        $this->createTable('component_formular_select', $fields);
        
        
        $fieldsOption['value'] = 'varchar(255) NOT NULL';
        $fieldsOption['select'] = 'varchar(255) NOT NULL';
        $this->createTable('component_formular_select_options', $fieldsOption);
    }
}