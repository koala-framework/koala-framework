<?php
class Vps_Data_Vpc_ComponentIcon extends Vps_Data_Abstract
{
    private $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row)
    {
        $generators = Vpc_Abstract::getSetting($this->_componentClass, 'generators');
        $classes = $generators['paragraphs']['component']; 
        if (!isset($classes[$row->component])) {
            return '';
        }        $class = $classes[$row->component];
        $name = Vpc_Abstract::getSetting($class, 'componentIcon');
        return (string)$name;
    }
}
