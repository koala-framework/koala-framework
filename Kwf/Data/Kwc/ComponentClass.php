<?php
class Kwf_Data_Kwc_ComponentClass extends Kwf_Data_Abstract
{
    private $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row)
    {
        $generators = Kwc_Abstract::getSetting($this->_componentClass, 'generators');
        $classes = $generators['paragraphs']['component'];
        if (!isset($classes[$row->component])) {
            return '';
        }
        return $classes[$row->component];
    }
}
