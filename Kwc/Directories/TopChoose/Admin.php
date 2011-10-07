<?php
class Vpc_Directories_TopChoose_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['directory_component_id'] = 'varchar(255) NOT NULL';
        $this->createFormTable('vpc_directories_top', $fields);
    }
}
