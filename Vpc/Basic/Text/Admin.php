<?php
class Vpc_Basic_Text_Admin extends Vpc_Admin
{
    public function setup()
    {
        $class = Vpc_Abstract::getSetting($this->_class, 'linkClass');
        Vpc_Admin::getInstance($class)->setup();
        
        $class = Vpc_Abstract::getSetting($this->_class, 'imageClass');
        Vpc_Admin::getInstance($class)->setup();

        $fields['content'] = 'text NOT NULL';
        $fields['content_edit'] = 'text NOT NULL';
        $this->createFormTable('vpc_basic_text', $fields);
    }
}