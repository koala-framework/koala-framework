<?php
class Vpc_Simple_Download_Setup extends Vpc_Setup_Abstract
{

    //habe hier das static entfernt .. problem
    public function setup()
    {
        $fields['path'] = 'varchar(255) NOT NULL';
        $this->createTable('component_simple_download', $fields);
    }
}
