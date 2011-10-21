<?php
class Vps_Data_Vpc_ComponentClass extends Vps_Data_Abstract
{
    private $_componentClass;
    private $_generatorKey;

    public function __construct($componentClass, $generatorKey = 'paragraphs')
    {
        $this->_componentClass = $componentClass;
        $this->_generatorKey = $generatorKey;
    }

    public function load($row)
    {
        $generators = Vpc_Abstract::getSetting($this->_componentClass, 'generators');
        $classes = $generators[$this->_generatorKey]['component'];
        if (!$row->getModel()->hasColumn('component') || !isset($classes[$row->component])) {
            return $this->_generatorKey;
        }
        return $classes[$row->component];
    }
}
