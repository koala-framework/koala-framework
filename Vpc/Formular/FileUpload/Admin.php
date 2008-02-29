<?php
class Vpc_Formular_FileUpload_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['types_allowed'] = 'varchar(255) NOT NULL';
        $fields['width'] = 'smallint(6) NOT NULL';
        $fields['max_size'] = 'int(10) NOT NULL';
        $this->createFormTable('vpc_formular_fileupload', $fields);
    }
}
