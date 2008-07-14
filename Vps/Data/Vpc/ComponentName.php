<?php
class Vps_Data_Vpc_ComponentName extends Vps_Data_Abstract
{
    private $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row)
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_componentClass, 'paragraphs');
        $class = $classes[$row->component];
        $name = Vpc_Abstract::getSetting($class, 'componentName');
        return str_replace('.', ' -> ', $name);
    }
}
