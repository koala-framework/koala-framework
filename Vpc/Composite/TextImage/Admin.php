<?php
class Vpc_Composite_TextImage_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');

        Vpc_Admin::getInstance($classes['text'])->setup();
        Vpc_Admin::getInstance($classes['image'])->setup();

        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $this->createFormTable('vpc_composite_textimage', $fields);
    }
}
