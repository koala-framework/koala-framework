<?php
class Vpc_Formular_FileUpload_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {   
        $fields['maxlength'] = 'varchar(6) NOT NULL';
        $fields['accept'] = 'varchar(255) NOT NULL';
        $fields['width'] = "smallint(6) NOT NULL'";
        $this->createTable('component_formular_fileupload', $fields);
    }
}