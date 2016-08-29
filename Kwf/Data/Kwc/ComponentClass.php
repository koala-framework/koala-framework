<?php
class Kwf_Data_Kwc_ComponentClass extends Kwf_Data_Abstract
{
    private $_componentClass;
    private $_generatorKey;

    public function __construct($componentClass, $generatorKey = 'paragraphs')
    {
        $this->_componentClass = $componentClass;
        $this->_generatorKey = $generatorKey;
    }

    public function load($row, array $info = array())
    {
        $generators = Kwc_Abstract::getSetting($this->_componentClass, 'generators');
        $classes = $generators[$this->_generatorKey]['component'];
        if (!$row->getModel()->hasColumn('component') || !isset($classes[$row->component])) {
            return $this->_generatorKey;
        }
        return $classes[$row->component];
    }
}
