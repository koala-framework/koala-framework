<?php
class Vpc_Basic_LinkTag_Admin extends Vpc_Admin
{
    public function getControllerConfig($class)
    {
        return array(
            'linkClasses' => Vpc_Abstract::getSetting($class, 'linkClasses')
        );
    }
    
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