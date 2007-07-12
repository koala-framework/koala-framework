<?php
class Vpc_Formular_FileUpload_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
        $fields['name'] = 'varchar(255) NOT NULL';
        $fields['length'] = 'varchar(6) NOT NULL';
        $fields['accept'] = 'varchar(255) NOT NULL';
        $fields['size'] = "smallint(6) NOT NULL default '50'";
        $this->createTable('component_formular_fileupload', $fields);
    }
}