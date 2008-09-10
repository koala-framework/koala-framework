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
        $generators = Vpc_Abstract::getSetting($this->_componentClass, 'generators');
        $classes =$generators['paragraphs']['component']; 
        return $classes[$row->component];
    }
}
