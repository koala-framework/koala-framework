<?php
class Kwf_Data_Kwc_ComponentIcon extends Kwf_Data_Abstract
{
    private $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row, array $info = array())
    {
        $generators = Kwc_Abstract::getSetting($this->_componentClass, 'generators');
        $classes = $generators['paragraphs']['component']; 
        if (!isset($classes[$row->component])) {
            return '';
        }        $class = $classes[$row->component];
        $name = Kwc_Abstract::getSetting($class, 'componentIcon');
        $name = new Kwf_Asset($name);
        return (string)$name;
    }
}
