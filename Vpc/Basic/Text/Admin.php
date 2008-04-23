<?php
class Vpc_Basic_Text_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');

        if ($classes['link']) Vpc_Admin::getInstance($classes['link'])->setup();
        if ($classes['image']) Vpc_Admin::getInstance($classes['image'])->setup();
        if ($classes['download']) Vpc_Admin::getInstance($classes['download'])->setup();

        $fields['content'] = 'text NOT NULL';
        $this->createFormTable('vpc_basic_text', $fields);
    }
}
