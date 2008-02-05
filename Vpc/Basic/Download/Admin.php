<?php
class Vpc_Basic_Download_Admin extends Vpc_Abstract_Composite_Admin
{
    public function setup()
    {
        parent::setup();
        $fields['infotext'] = 'text';
        $this->createFormTable('vpc_basic_download', $fields);
    }
}
