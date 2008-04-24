<?php
class Vps_Form_Field_ComponentContainer extends Vps_Form_Field_Abstract
{
    private $_component;

    public function __construct($component)
    {
        $this->_component = $component;
        $this->_init();
    }
    public function getTemplateVars($values)
    {
        return array('component' => $this->_component->getTemplateVars());
    }
}
