<?php
class Kwc_Directories_TopChoose_Admin extends Kwc_Admin
{
    public function setup()
    {
        $fields['directory_component_id'] = 'varchar(255) NOT NULL';
        $this->createFormTable('kwc_directories_top', $fields);
    }
}
