<?php
class Vpc_Basic_LinkTag_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'link');
        foreach ($classes as $class) {
            Vpc_Admin::getInstance($class)->setup();
        }

        $fields['component'] = "VARCHAR(255) NOT NULL";
        $this->createFormTable('vpc_basic_linktag', $fields);
    }
}
