<?php
class Vpc_Basic_LinkTag_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vpc.Basic.LinkTag.Panel';
    }
    
    public function setup()
    {
        $fields['link_class'] = "VARCHAR(255) NOT NULL";
        $this->createFormTable('vpc_basic_linktag', $fields);
    }
}