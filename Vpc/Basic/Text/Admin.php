<?php
class Vpc_Basic_Text_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');

        Vpc_Admin::getInstance($classes['link'])->setup();
        Vpc_Admin::getInstance($classes['image'])->setup();
        
        $fields['content'] = 'text NOT NULL';
        $fields['content_edit'] = 'text NOT NULL';
        $this->createFormTable('vpc_basic_text', $fields);
    }
}