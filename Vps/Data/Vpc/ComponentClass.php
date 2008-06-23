<?php
class Vps_Data_Vpc_ComponentClass extends Vps_Data_Abstract
{
    private $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row)
    {
        $classes = Vpc_Abstract::getSetting($this->_componentClass, 'childComponentClasses');
        return $class = $classes[$row->component];
    }
}
